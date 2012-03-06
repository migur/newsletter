<?php

/**
 * The controller for smtpprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer.sender');

JLoader::import('tables.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerSmtpprofile extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('savenclose', 'save');
	}

	protected function checkEditId($context, $id) {
		return true;
	}

	/**
	 * Save action
	 */
	public function save()
	{
		$form = JRequest::getVar('jform');
		$model = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
		$model->load($form['smtp_profile_id']);
		
		if ($model->isJoomlaProfile()) {
			$buffer = array_merge($model->toArray(), $form);
			$buffer['params'] = array_merge((array)$model->params, $form['params']);
			JRequest::setVar('jform', $buffer, 'post');
		}

		parent::save();

		$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
	}

	
	/**
	 * Redirection after standard saving
	 *
	 * @return void
	 * @since 1.0
	 */
	public function delete()
	{

		parent::delete();

		$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
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

		if ($recordId !== null) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	public function checkConnection()
	{
		$smtpSettings = (object)JRequest::getVar('jform');

		$sender = new MigurMailerSender();
		$res = $sender->checkConnection($smtpSettings);
		
		echo json_encode(array(
			'status' => $res? 'ok' : 'Unable to connect'
		));
		jexit();
	}
	
}

