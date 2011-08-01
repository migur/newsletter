<?php

/**
 * The controller for lsit view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerList extends JControllerForm
{

	/**
	 *
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
		return true;
	}

	/**
	 * Save the configuration
	 * @return	boolean
	 * @since	1.0
	 */
	function save()
	{
		if (parent::save()) {
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
					break;
			}

			return true;
		} else {
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $key), false));
		}

		return false;
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
	 * Is used for standard upload of file.
	 * @since  1.0
	 * @return void
	 */
	public function upload()
	{

		$listId = JRequest::getInt('list_id', 0);
		$subtask = JRequest::getString('subtask', 'import');

		if ($listId > 0) {

			$uploader = JModel::getInstance('file', 'NewsletterModel');
			$data = $uploader->upload(array(
					'overwrite' => true,
					'filedataName' => 'Filedata-' . $subtask
				));

			if (!empty($data['file'])) {

				// get the column names from uploaded file
				$arr = file($data['file']['filepath']);

				$data['fields'] = explode(',', $arr[0]);
			}
		}

		if (empty($data)) {
			$data = array();
		}

		$sess = JFactory::getSession();
		$sess->set('list.' . $listId . '.file.uploaded', $data);

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($listId, 'list_id') . '&subtask=' . $subtask, false));
		return;
	}

	/**
	 * Assign the subscriber to the list
	 * @since  1.0
	 * @return void
	 */
	public function assignGroup()
	{
		if (JRequest::getMethod() == "POST") {

			$model = $this->getModel('Subscriber', 'NewsletterModel');

			$subscribers = JRequest::getVar('cid', null, 'post');
			$lists = json_decode(JRequest::getVar('list_id', null, 'post'));

			//var_dump($subscribers, $listId); die();
			if (!empty($lists) && !empty($subscribers)) {
				foreach($lists as $listId) {
					foreach ($subscribers as $subscriberId) {

						$data = (object) array(
								'subscriber_id' => $subscriberId,
								'list_id' => $listId
						);


						if ($model->assignToList($data)) {
							$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_SUCCESS"));
						} else {
							$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_FAILED"), 'error');
							break(2);
						}
					}
				}
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=subscribers', false));
	}

	/**
	 * Unbind the subscriber to the list
	 * @since  1.0
	 * @return void
	 */
	public function unbindGroup()
	{
		if (JRequest::getMethod() == "POST") {

			$model = $this->getModel('Subscriber', 'NewsletterModel');

			$subscribers = JRequest::getVar('cid', null, 'post');
			$lists = json_decode(JRequest::getVar('list_id', null, 'post'));

			//var_dump($subscribers, $listId); die();
			if (!empty($lists) && !empty($subscribers)) {
				foreach($lists as $listId) {
					foreach ($subscribers as $subscriberId) {

						$data = (object) array(
								'subscriber_id' => $subscriberId,
								'list_id' => $listId
						);


						if ($model->unbindFromList($data)) {
							$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
						} else {
							$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
							break(2);
						}
					}
				}
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=subscribers', false));
	}
}

