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
jimport('joomla.session.session');

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.sent',           JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.subscriber',     JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.queue',          JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of the cron controller. Handles the  request of a "trigger" from remote server.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterControllerCron extends JControllerForm
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
	 * Sends the bulk of letters to queued subscribers.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function send()
	{
		ob_start();
            
		$config   = JComponentHelper::getParams('com_newsletter');
		$lastExec =        $config->get('mailer_cron_last_execution_time');
		$isExec   = (bool) $config->get('mailer_cron_is_executed');
		$doSave   = (bool) $config->get('newsletter_save_to_db');
		$interval = (int)  $config->get('mailer_cron_interval');
		$count    = (int)  $config->get('mailer_cron_count');
                
		$forced = JRequest::getBool('forced', false);
		if($forced) {
                    
				$conf = JFactory::getConfig();
				$handler = $conf->get('session_handler', 'none');
				$sessId = JRequest::getVar(JRequest::getString('sessname', ''), false, 'COOKIE');
				if(empty($sessId)){
					ob_end_clean();
				echo 'Unknown session';
					jexit();
				}    
				$data = JSessionStorage::getInstance($handler, array())->read($sessId);
				session_decode($data);
        	    $user = $_SESSION['__default']['user'];
            	    $levels = $user->getAuthorisedGroups();
	            if ( max($levels) < 7 ) {
                        ob_end_clean();
	                echo 'Unknown session';
                        jexit();
                    }    
                }
                
                

		$table = JTable::getInstance('jextension', 'NewsletterTable');
		$lastExec = !empty($lastExec) ? strtotime($lastExec) : 0;
		$interval = $interval * 60;

		if ( ($lastExec + $interval < time() && !$isExec) || $forced) {

			// set the isExecuted flag
			if ($table->load(array('name' => 'com_newsletter'))) {
				$table->addToParams(array('mailer_cron_is_executed' => 1));
				$table->store();
			}

			$db = JFactory::getDbo();
			$db->debug(0);
			$query = $db->getQuery(true);
			$query->select('newsletter_id, subscriber_id')
				->from('#__newsletter_queue')
				->where('state=1')
				->group('newsletter_id, subscriber_id')
				->order('newsletter_id');
			$list = $db->setQuery($query, 0, $count)->loadAssocList();

			$ret = array();
			if (!empty($list)) {

				$queue      = JTable::getInstance('queue', 'NewsletterTable');
				$subscriber = JTable::getInstance('subscriber', 'NewsletterTable');

				$mailer = new MigurMailer();

				foreach ($list as $item) {

					$subscriber->load($item['subscriber_id']);
                                        
					$type  = ($subscriber->html == 1) ? 'html' : 'plain';
					
					$letter = $mailer->send(array(
						'subscriber' => $subscriber,
						'newsletter_id' => $item['newsletter_id'],
						'type'          => $type
					));

					$ret[] = array(
						'newsletter_id' => $item['newsletter_id'],
						'email' => $subscriber->email,
						'subscriber_id' => $subscriber->subscriber_id,
						'state' => (int)$letter->state,
						'error' => $letter->error
					);


					// Set up the sending start time

					$nl = JTable::getInstance('newsletter', 'NewsletterTable');
					$nl->load(array('newsletter_id' => $item['newsletter_id']));

					if ( $nl->sent_started == '0000-00-00 00:00:00' || strtotime($nl->sent_started) <= 0 ) {
						$nl->save(array(
							'sent_started' => date('Y-m-d H:i:s')
						));
					}
					unset($nl);

					// Update the queue item after success mailing
					$st = $letter->state? 0 : 2;
                                        
					$db->setQuery(
							'UPDATE #__newsletter_queue SET state='.$st
							.' WHERE newsletter_id=' . $item['newsletter_id']
							.' AND subscriber_id=' . $item['subscriber_id']);
					$db->query();

					// Get all records which refers to the current user and the current newsletter
					$query = $db->getQuery(true);
					$query->select('*')
						->from('#__newsletter_queue')
						->where('newsletter_id=' . $item['newsletter_id'])
						->where('subscriber_id=' . $item['subscriber_id']);
					$group = $db->setQuery($query)->loadAssocList();

					// Process all lists in which the user is present
					foreach ($group as $groupItem) {

						// Add notes to history for each list
						$history = JTable::getInstance('history', 'NewsletterTable');
						$history->save(array(
							'newsletter_id' => $groupItem['newsletter_id'],
							'subscriber_id' => $groupItem['subscriber_id'],
							'list_id'       => $groupItem['list_id'],
							'date'    => date('Y-m-d H:i:s'),
							'action'  => $letter->state?
								NewsletterTableHistory::ACTION_SENT :
								NewsletterTableHistory::ACTION_BOUNCED,
							'text'    => ''
						));
						unset($history);


						// Add the sended letter to the sents for each list
						if ($doSave) {
							$sent = JTable::getInstance('sent', 'NewsletterTable');
							$sent->save(array(
								'newsletter_id' => $groupItem['newsletter_id'],
								'subscriber_id' => $groupItem['subscriber_id'],
								'list_id'       => $groupItem['list_id'],
								'sent_date' => date('Y-m-d H:i:s'),
								'bounced'   => ($letter->state)?
									NewsletterTableSent::BOUNCED_NO :
									NewsletterTableSent::BOUNCED_SOFT,

								'html_content' => ($type == 'html') ? $letter->content : "",
								'plaintext_content' => ($type == 'plain') ? $letter->content : ""
							));
							unset($sent);
						}
					}
				}
			}

			if ($table) {
				$table->addToParams(array(
					'mailer_cron_is_executed' => 0,
					'mailer_cron_last_execution_time' => date('Y-m-d H:i:s'),
                                        'mailer_force' => false
				));
				$table->store();
			}

			$response = json_encode(array(
				'data' => $ret,
				'count' => count($list),
				'error' => ''
			));
			
		} else {
                    
			$response = json_encode(array(
				'error' => 'The interval is not exedeed'
			));
		}
                
		ob_end_clean();
		echo $response;
		jexit();
	}
	
	/**
	 * 
	 */
	public function bounced(){
		
		$mailer = new MigurMailer();
		$subscriber = JTable::getInstance('subscriber', 'NewsletterTable');
		$subscriber->load('8316');

		//var_dump($subscriber); die;
		$subscriber->email = 'andreyalek2@gmail.com';
		var_dump(
			$mailer->send(array(
				'subscriber' => $subscriber,
				'newsletter_id' => 130,
				'type' => 'html'
			))
		);
		
		die;
	}
	
	public function processbounced()
	{
		$bounceds = JModel::getInstance('Bounceds', 'NewsletterModel');
		
		$mailboxes = $bounceds->getMailboxesForBounsecheck();
		
		$processed = 0;
		$errors = array();
		// Trying to check all bounces
		foreach($mailboxes as $mailbox) {
		
			if (!MailHelper::connect($mailbox)) {
				$errors[] = $mailbox['username'] . ':' . MailHelper::getMailboxError();
				continue;
			}
			
			$mails = MailHelper::getBouncedList();

			if (empty($mails)) 
			{
				echo json_encode(array(
					'error' => '', 
					'count' => 0,
					'message' => 'No mails to process'
				));
				jexit();
			}

			foreach($mails as &$mail) 
			{
				if (!empty($mail->subscriber_id) && !empty($mail->newsletter_id) && !empty($mail->bounce_type)) 
				{
					$sent = JModel::getInstance('Sent', 'NewsletterModel');
					$sent->setBounced($mail->subscriber_id, $mail->newsletter_id, $mail->bounce_type);

					$history = JModel::getInstance('History', 'NewsletterModel');
					$history->setBounced($mail->subscriber_id, $mail->newsletter_id, $mail->bounce_type);

					$queue = JTable::getInstance('Queue', 'NewsletterTable');
					$queue->setBounced($mail->subscriber_id, $mail->newsletter_id, NewsletterTableQueue::STATE_BOUNCED);

					if ($mail->msgnum > 0) {
						MailHelper::deleteMail($mail->msgnum);
						$processed++;
					}	
				}
			}
			
			MailHelper::closeMailbox();
		}
		
		echo json_encode(array(
			'error' => $errors,
			'count'  => $processed,
			'mailboxes' => count($mailboxes)
		));
		
		jexit();
	}
	
	public function processbouncedtest(){
	
		define('_PATH_BMH', JPATH_LIBRARIES.DS.'migur'.DS.'library'.DS.'mailer'.DS.'phpmailer'.DS);

		include(_PATH_BMH . 'class.phpmailer-bmh.php');
		//include(_PATH_BMH . 'callback_echo.php');

		// testing examples
		$bmh = new BounceMailHandler();
		//$bmh->action_function    = 'callbackAction'; // default is 'callbackAction'
		$bmh->verbose            = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
		//$bmh->use_fetchstructure = true; // true is default, no need to speficy
		$bmh->testmode           = true; // false is default, no need to specify
		//$bmh->debug_body_rule    = true; // false is default, no need to specify
		//$bmh->debug_dsn_rule     = true; // false is default, no need to specify
		//$bmh->purge_unprocessed  = false; // false is default, no need to specify
		$bmh->disable_delete     = true; // false is default, no need to specify

		/*
		 * for local mailbox (to process .EML files)
		 */
		//$bmh->openLocalDirectory('/home/email/temp/mailbox');
		//$bmh->processMailbox();

		/*
		 * for remote mailbox
		 */
		$bmh->mailhost           = 'imap.gmail.com'; // your mail server
		$bmh->mailbox_username   = 'andreyalek2@gmail.com'; // your mailbox username
		$bmh->mailbox_password   = 'gmail83twenty'; // your mailbox password
		$bmh->port               = 993; // the port to access your mailbox, default is 143
		$bmh->service            = 'imap'; // the service to use (imap or pop3), default is 'imap'
		$bmh->service_option     = 'ssl'; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
		$bmh->boxname            = 'INBOX'; // the mailbox to access, default is 'INBOX'
		//$bmh->moveHard           = true; // default is false
		//$bmh->hardMailbox        = 'INBOX.hardtest'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
		//$bmh->moveSoft           = true; // default is false
		//$bmh->softMailbox        = 'INBOX.softtest'; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
		//$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'

		/*
		 * rest used regardless what type of connection it is
		 */
		$bmh->openMailbox();
		$bmh->processMailbox();
		
		die;
	}
}

