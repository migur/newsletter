<?php

/**
 * The controller for newsletter json requests.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.download', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.html.file', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerNewsletter extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('savenclose', 'save');
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		//TODO: Need to check and remove this function
		return true;
	}

	/**
	 * Method to check whether an ID is in the edit list.
	 *
	 * @param	string	$context	The context for the session storage.
	 * @param	int		$id			The ID of the record to add to the edit list.
	 *
	 * @return	boolean	True if the ID is in the edit list.
	 * @since	1.0
	 */
	protected function checkEditId($context, $id)
	{
		//TODO: Need to check and remove this function
		return true;
	}

	/**
	 * Save the configuration
	 * @return	void
	 * @since	1.0
	 */
	function save()
	{
		$nsid = JRequest::getVar('newsletter_id', '0');

		$type = JRequest::getVar('task');

		// We can save NEW newsletter (create it) or autosave an existing letter
		if (!empty($nsid) || $type == 'save') {

			$data = JRequest::getVar('jform', array(), 'post', 'array');

			// If the type is not changeable then replace type as now (for success validation).
			if (!empty($nsid)) {
				$nl = NewsletterHelper::get($nsid);
				if (!$nl['type_changeable']) {
					$data['type'] = $nl['type'];
					JRequest::setVar('jform', $data, 'post');
				}
			}

			if (parent::save()) {

				$nsid = $this->newsletterId;

				$context = "$this->option.edit.$this->context";

				$htmlTpl = (object) json_decode($data['htmlTpl']);
				$plugins = (array) json_decode($data['plugins']);
				$htmlTpl->extensions = array_merge($htmlTpl->extensions, $plugins);
				$newExtsModel = $this->getModel('newsletterext');
				if ($newExtsModel->rebindExtensions(
						$htmlTpl->extensions,
						$nsid
				)) {
					
				} else {
					$error = $newExtsModel->getError();
				}
			} else {
				$error = $this->getError();
			}
		} else {
			$error = JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY');
		}

		$this->setRedirect(null);
		if (empty($error)) {
			$error = JFactory::getApplication()->getMessageQueue();
		}

		echo json_encode(array(
			'state' => (!empty($error)) ? $error : 'ok',
			'newsletter_id' => $nsid
			)
		);
		return;
	}

	/**
	 * Saves the Id of current record to give
	 * access for other methods of controller to it
	 *
	 * @param <type> $model - the model object
	 * @param <type> $data  - saved data.
	 * 
	 * @return void
	 * @since 1.0
	 */
	protected function postSaveHook($model, $data)
	{
		$this->newsletterId = $model->getState($model->getName() . '.id');
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl = JRequest::getCmd('tmpl', 'component');
		$layout = JRequest::getCmd('layout');
		$append = '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout) {
			$append .= '&layout=' . $layout;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Get data for JS autocompleter to the client
	 *
	 * @since 1.0
	 */
	public function autocomplete()
	{
		echo json_encode(AutocompleterHelper::getSubscribers());
	}

	// TODO: Check and remove this method. Mailing is with queue now.
//	public function sendToList()
//	{
//
//		$listId = JRequest::getInt('list_id');
//
//		$subscribers = JModel::getInstance('list', 'NewsletterModel')->getSubscribers(JRequest::get('list_id'));
//		if (!$subscribers) {
//			echo json_encode(array(
//				'state' => '0',
//				'error' => 'Unable to load list',
//				'list_id' => $listId
//			));
//			return;
//		}
//
//		$mailer = new MigurMailer();
//		$mailer->sendToList(array(
//			'subscribers' => $subscribers,
//			'newsletter_id' => JRequest::getInt('newsletter_id'),
//			'list_id' => $listId
//		));
//	}

	/**
	 * Handles the configuration "Clear sent" button
	 * Clear all data from table "_sent".
	 * @return void
	 * @since  1.0
	 */
	public function clearSent()
	{
		$table = JTable::getInstance('sent', 'NewsletterTable');
		$res = $table->deleteAll();
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
		));
		unset($table);
	}

	/**
	 * Handles the unbinding of a file attached to a newsletter
	 *
	 * @return void
	 * @since  1.0
	 */
	public function fileUnbind()
	{

		$id = JRequest::getInt('download_id');
		if (empty($id)) {
			echo json_encode(array(
				'state' => 0,
				'error' => 'Parameters are mising',
			));
			return;
		}

		$table = JTable::getInstance('downloads', 'NewsletterTable');
		$res = $table->delete($id);
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
		));
	}

	/**
	 * Handles the binding of a file attached to a newsletter
	 *
	 * @return void
	 * @since  1.0
	 */
	public function fileAttach()
	{

		$filename = JRequest::getString('filename');
		$nId = JRequest::getInt('newsletter_id');

		if (empty($nId) || empty($filename)) {
			echo json_encode(array(
				'state' => 0,
				'error' => 'Parameters are mising',
			));
			return;
		}

		$table = JTable::getInstance('downloads', 'NewsletterTable');
		$res = $table->save(array('filename' => $filename, 'newsletter_id' => $nId));

		$file = new stdClass();

		if ($res) {
			$file->downloads_id = $table->downloads_id;
			$file->newsletter_id = $nId;
			$file->filename = $filename;
			DownloadHelper::getAttributes($file);
			$file->size = JHtml::_('file.size', $file->size, 'kb/mb');
		}
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
			'data' => $file
		));
	}

}

