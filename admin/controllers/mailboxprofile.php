<?php

/**
 * The controller for mailboxprofile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer.mailbox');

JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');


class NewsletterControllerMailboxprofile extends JControllerForm
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
		//TODO: Remove and check the method
		return true;
	}

	/**
	 * Method override to check if you can save an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0
	 */
	protected function allowSave($data = array(), $key = 'id')
	{
		//TODO: Remove and check the method
		return true;
	}

	/**
	 * Redirection after standard saving
	 *
	 * @return void
	 * @since 1.0
	 */
	public function save()
	{
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

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	public function checkConnection()
	{
		$options = JRequest::getVar('jform');
		
		$mailbox = new MigurMailerMailbox($options);
		
		$res = $mailbox->connect();

		if ($res) {
			$mailbox->close();
		}

		$err = $mailbox->getLastError();
		
		if (empty($err)) {
			$err = 'Unknown error';
		}
		
		imap_errors(); 
		imap_alerts();

		echo json_encode(array(
			'status' => $res? 'ok' : $err
		));

		jexit();
	}
	
}

