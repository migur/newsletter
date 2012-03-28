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
	public function track() {

        // Required data
		$subkey       = JRequest::getString('uid', '');
		$newsletterId = JRequest::getInt('nid', 0);
		$action       = JRequest::getString('action', '');
		$link         = base64_decode(urldecode(JRequest::getVar('link', '')));

        // Optional data
		$listId = JRequest::getInt('lid', 0);
        $listId = !empty($listId)? $listId : null;
        
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

			// If this is a "clicked" event we should save the link
			$text = ($actionCode == NewsletterTableHistory::ACTION_CLICKED)?
				$link : "";

			// Track the event
			// If type of action is ACTION_OPENED then check it 
			// should be only one in the DB for sid-nid
			if ($actionCode == NewsletterTableHistory::ACTION_OPENED) {

				$res = $table->load(array(
					'subscriber_id' => (int)$subscriber->subscriber_id,
					'newsletter_id' => (int)$newsletterId,
					'action'        => $actionCode
				));

				if (!empty($table->history_id)) {
					throw new Exception('no need to track');
				} 
			}

			$res = $table->save(array(
				'subscriber_id' => (int)$subscriber->subscriber_id,
				'list_id'       => $listId, // May be null (need for FK)!
				'newsletter_id' => (int)$newsletterId,
				'date'          => date('Y-m-d H:i:s'),
				'action'        => $actionCode,
				'text'          => addslashes($link)
			));
            
            if (!$res) {
                $err = $table->getError();
                $err = ($err instanceof Exception)? $err->getMessage() : $err;
                throw new Exception('Error saving data: '.$err);
            }
				
		} catch(Exception $e) {
            
			if ($e->getMessage() != 'no need to track') {
                
				// For debug
                LogHelper::addDebug('Tracking failed', LogHelper::CAT_TRACKING,  
                    array(
                        'Message' => $e->getMessage(),
                        'Data' => JRequest::get())
                );
                
                jexit();
                
			} else {

				// For debug
                LogHelper::addDebug('Tracking', LogHelper::CAT_TRACKING,  
                    array(
                        'Message' => 'No need to track',
                        'data' => JRequest::get())
                );
                
            }
		}
		
        LogHelper::addDebug('Tracking', LogHelper::CAT_TRACKING, JRequest::get());
        
		// Redirect it!
		if (!empty($link)) {
			$this->setRedirect($link);
		}
	}
}

