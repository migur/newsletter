<?php

/**
 * The cron controller file.
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
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of the cron controller. Handles the  request of a "trigger" from remote server.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterControllerNewsletter extends JControllerForm
{

	/**
	 * The constructor of a class
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Renders the newsletter for an subscriber.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function render()
	{

		/*
		 * Get the info about current user.
		 * Check if the user is admin.
		 */

		//TODO: Get the admin session...
		
		/*
		 *  Let's render the newsletter.
		 *  If subscriber's email is provided then add info about this user
		 *  to environment
		 */

		$newsletterId = JRequest::getVar('newsletter_id');
		$type         = JRequest::getVar('type');
		$email        = urldecode(JRequest::getVar('email'));
		$alias        = JRequest::getString('alias', null);

		if (!empty($alias)) {
			$newslettter = NewsletterHelper::getByAlias($alias);
			$newsletterId = $newslettter['newsletter_id'];
		}	
		
		if (empty($newsletterId)) {
			echo json_encode(array(
				'state' => '0',
				'error' => 'The newsletter id is absent',
			));
			return;
		}

		if (empty($type)) {
			echo json_encode(array(
				'state' => '0',
				'error' => "The type is absent",
			));
			return;
		}


		$mailer = new MigurMailer();
		
		// emulate user environment
		SubscriberHelper::saveRealUser();
		SubscriberHelper::emulateUser(array('email' => $email));

		// render the content of letter for each user
		$res = $mailer->render(array(
			'type' => $type,
			'newsletter_id' => $newsletterId,
		));

		SubscriberHelper::restoreRealUser();

		echo $res; die;
//		echo json_encode(array(
//			'state' => '1',
//			'error' => $res,
//		));
	}


	/**
	 * Render and send the letter to the selected emails
	 * Only for preview!!!! This method dont use the DB data.
	 * It gets data from REQUEST:
	 *	- params,
	 *  - title,
	 *  - showtitle,
	 *  - extension_id(the id of a module in db)
	 *  - native
	 *
	 * @return void
	 * @since  1.0
	 */
	public function rendermodule()
	{
		$native     = JRequest::getString('native');
		$id         = JRequest::getString('extension_id');
		$params     = JRequest::getVar('params', array(), 'post', 'array');
		$title      = JRequest::getString('title');
		$showTitle  = JRequest::getString('showtitle');

		$modules = MigurModuleHelper::getSupported(array(
			'extension_id' => $id,
			'native'       => $native
		));

		$module = $modules[0];

		// Override needed data
		$module->params     = json_encode((object)$params);
		$module->title      = $title;
		$module->showtitle  = $showTitle;

		$content = MigurModuleHelper::renderModule($modules[0]);

		echo $content; die;
	}
	/**
	 * Render and send the letter to the selected emails
	 *
	 * @return void
	 * @since  1.0
	 */
	public function sendPreview()
	{
		$emails = JRequest::getVar('emails', array());
		$newsletterId = JRequest::getVar('newsletter_id');
		$type = JRequest::getVar('type');

		if (empty($type) || empty($newsletterId)) {
			NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_RUQUIRED_MISSING'));
		}

		if (empty($emails)) {
			NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_ADD_EMAILS'));
		}
		
		$data = array(
			'newsletter_id' => $newsletterId,
			'type' => $type,
			'tracking' => true
		);
		
		foreach ($emails as $email) {
			$data['subscribers'][] = SubscriberHelper::getByEmail($email[1]);
		}

		$mailer = new MigurMailer();
		if(!$mailer->sendToList($data)) {

			$errors = $mailer->getErrors();
			
			LogHelper::addDebug('Sending of preview was failed.', 
				LogHelper::CAT_MAILER,
				array(
					'Errors' => $errors,
					'Emails' => $emails));
			
			NewsletterHelper::jsonError($errors, $emails);
		}
		
		
		LogHelper::addDebug('Preview was sent successfully.', 
			LogHelper::CAT_MAILER,
			array('Emails' => $emails));
		
		NewsletterHelper::jsonMessage('ok', $emails);
	}
}

