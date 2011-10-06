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

/**
 * Class of the cron controller. Handles the  request of a "trigger" from remote server.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterControllerNewsletter extends JControllerForm
{
	/**
	 * Render and send the letter to the selected emails
	 *
	 * @return void
	 * @since  1.0
	 */
//	public function sendPreview()//deprecated
//	{
//		$emails = JRequest::getVar('emails');
//		$newsletterId = JRequest::getVar('newsletter_id');
//		$type = JRequest::getVar('type');
//
//		$data = array(
//			'newsletter_id' => $newsletterId,
//			'type' => $type,
//			'tracking' => true
//		);
//		
//		foreach ($emails as $email) {
//			$data['subscribers'][] = SubscriberHelper::getByEmail($email[1]);
//		}
//
//		$mailer = new MigurMailer();
//		$mailer->sendToList($data);
//	}

	public function track() {

		$subkey       = JRequest::getString('uid', '');
		$newsletterId = JRequest::getInt('nid', 0);
		$listId       = JRequest::getInt('lid', 0);
		$action       = JRequest::getString('action', '');
		$link         = base64_decode(urldecode(JRequest::getVar('link')));

		try {
			// Check the uid
			$subscriber = SubscriberHelper::getBySubkey($subkey);
			if (empty($subscriber->subscriber_id)) {
				throw new Exception('User is absent');
			}

			// Determine the action
			$table = JTable::getInstance('history', 'NewsletterTable');
			$actionCode = $table->getActionCode($action);
			if ($actionCode === false) {
				throw new Exception('Unknown action');
			}

			$text = "";
			// If this is a "clicked" event we should save the link
			if ($actionCode == NewsletterTableHistory::ACTION_CLICKED && !empty($link)) {

				// Track the event
				$db = JFactory::getDbo();
				$db->setQuery(
					'INSERT IGNORE INTO #__newsletter_sub_history SET ' .
						'subscriber_id=' . (int)$subscriber->subscriber_id . ', ' .
						'list_id=' . (int)$listId . ', ' .
						'newsletter_id=' . (int)$newsletterId . ', ' .
						'date="' . date('Y-m-d H:i:s') . '", ' .
						'action=' . $actionCode . ', ' .
						'text="' . addslashes($link) . '"'
				);
				$db->query();
			}
		} catch(Exception $e) {}
		// Redirect it!
		if (!empty($link)) {
			$this->setRedirect($link);
		}
	}
}

