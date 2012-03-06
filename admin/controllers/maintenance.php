<?php

/**
 * The controller for automailing view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

JLoader::import('helpers.environment', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerMaintenance extends JControllerForm
{

	/**
	 * All checks of environment helper
	 *
 	 * @return string json
	 */
	public function checkEnvironment()
	{
		$checks = EnvironmentHelper::getAvailableChecks();
		
		$res = array();
		foreach($checks as $check) {
			
			// Skip this to check in checkDb()
			if ($check == 'checkUserConflicts') continue;
			
			
			$rs = EnvironmentHelper::$check();
			
			$res[] = array(
				'text' => JText::_('COM_NEWSLETTER_MAINTENANCE_'.strtoupper($check)),
				'type' => $rs);
		}
		
		NewsletterHelper::jsonMessage('checkEnvironment', $res);
	}

	
	/**
	 * Gets last used schema from #_schema
	 * 
	 * @return string json
	 */
	public function checkDb()
	{
		$res = array();
		
		
		
		// 1. Check the schema version
		$man = NewsletterHelper::getManifest();
		$version = $man->version;
		$schema = EnvironmentHelper::getLastSchema();

		$sc = (version_compare($schema, $version) <= 0);
		
		$res[] = array(
			'text' => JText::_('COM_NEWSLETTER_MAINTENANCE_CHECKSCHEMA').': '.$schema,
			'type' => $sc);

		
		
		// 2. Check conflicts
		$count = EnvironmentHelper::getConflictsCount();
		
		$res[] = array(
			'text' => JText::_('COM_NEWSLETTER_MAINTENANCE_CHECKUSERCONFLICTS').' '.
					  JText::sprintf('COM_NEWSLETTER_CONFLICTS_FOUND', $count),
			'type' => ($count == 0) );

		
		
		// 3. Remove all died rows
		$dbo = JFactory::getDbo();
		$dbo->setQuery(
			'DELETE FROM #__newsletter_subscribers '.
			'USING #__newsletter_subscribers '.
			'LEFT JOIN #__users AS u ON u.id = #__newsletter_subscribers.user_id '.
			'WHERE #__newsletter_subscribers.email="" AND #__newsletter_subscribers.user_id > 0 AND u.id IS NULL');
		if($dbo->query()){
			
			$diedCnt = $dbo->getAffectedRows();

			$res[] = array(
				'text' => JText::_('COM_NEWSLETTER_MAINTENANCE_CHECKDIEDROWS').':'.$diedCnt,
				'type' => true);
		} else {
			$res[] = array(
				'text' => JText::_('COM_NEWSLETTER_MAINTENANCE_CHECKDIEDROWS'),
				'type' => false);
		}

		
		
		// 3. Return data
		NewsletterHelper::jsonMessage('checkDb', $res);
	}

	
	
	/**
	 * Check connections to ALL smtp servers.
	 * With/without certificate validation
	 * 
	 * @return string json
	 */
	public function checkSmtps()
	{
		$res = array();
		
		$manager = JModel::getInstance('Smtpprofiles', 'NewsletterModel');
		$smtpps = $manager->getAllItems();
		
		if (!empty($manager)) {
			
			jimport('migur.library.mailer.sender');
			$sender = new MigurMailerSender();
			$model = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
			
			foreach($smtpps as $smtpp){
				
				$model->load($smtpp->smtp_profile_id);
				
				$res[] = array(
					'text' => JText::sprintf('COM_NEWSLETTER_MAINTENANCE_CHECKSMTP', $model->smtp_profile_name),
					'type'  => $sender->checkConnection($model->toObject()));
			}
		}
		
		// Return data
		NewsletterHelper::jsonMessage('checkSmtps', $res);
	}

	
	/**
	 * Check connections to ALL mailbox servers.
	 * 
	 * @return string json
	 */
	public function checkMailboxes()
	{
		$res = array();

		$manager = JModel::getInstance('Mailboxprofiles', 'NewsletterModel');
		$mailboxes = $manager->getAllItems();
		
		if (!empty($mailboxes)) {

			jimport('migur.library.mailer.mailbox');

			foreach($mailboxes as $mailboxSettings) {
				
				$text = JText::sprintf('COM_NEWSLETTER_MAINTENANCE_CHECKMAILBOX', $mailboxSettings->mailbox_profile_name).'...';
				
				$mailboxSettings = (array)$mailboxSettings;
				$mailbox = new MigurMailerMailbox($mailboxSettings);

				$errors = array();

				if($mailbox->connect()) {
					$mailbox->close();
				} else {

					$errors[] = JText::_('COM_NEWSLETTER_UNABLE_TO_CONNECT');
					$errors[] = $mailbox->getLastError();

					if (!$mailbox->protocol->getOption('noValidateCert')) {
						$mailbox->protocol->setOption('noValidateCert', true);

						$errors[] = JText::_('COM_NEWSLETTER_TRYING_TO_CONNECT_WITHOUT_CERT');

						if ($mailbox->connect()) {
							$mailbox->close();
							$errors[] = JText::_('COM_NEWSLETTER_OK_CHECK_YOUR_CERT');
						} else {
							$errors[] = JText::_('COM_NEWSLETTER_FAILED') . '. ' . $mailbox->getLastError();
						}
					}
				}	

				if (count($errors) > 0) {
					$text .= '<br/>'.implode('<br/>', $errors);
				}	

				imap_errors(); 
				imap_alerts();
				
				$res[] = array(
					'text' => $text,
					'type'  => count($errors) == 0);
			}
		}	

		// Return data
		NewsletterHelper::jsonMessage('checkMailboxes', $res);
	}
}

