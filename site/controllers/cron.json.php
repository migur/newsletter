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

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.sent',           JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.subscriber',     JPATH_COMPONENT_ADMINISTRATOR, '');

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
		$config   = JComponentHelper::getParams('com_newsletter');
		$lastExec =        $config->get('mailer_cron_last_execution_time');
		$isExec   = (bool) $config->get('mailer_cron_is_executed');
		$doSave   = (bool) $config->get('newsletter_save_to_db');
		$interval = (int)  $config->get('mailer_cron_interval');
		$count    = (int)  $config->get('mailer_cron_count');

		$table = JTable::getInstance('jextension', 'NewsletterTable');
		$lastExec = !empty($lastExec) ? strtotime($lastExec) : 0;
		$interval = $interval * 60;

		if ($lastExec + $interval < time() && !$isExec) {

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

					$res[] = $item['newsletter_id'];
					$res['state'] = ($letter->state) ? 'success' : $mailer->getErrors();
					$ret[] = array(
						'newsletter_id' => $item['newsletter_id'],
						'email' => $subscriber->email,
						'state' => (int)$letter->state,
						'error' => $letter->error
					);


					// Set up the sending start time

					$nl = JTable::getInstance('newsletter', 'NewsletterTable');
					$nl->load(array('newsletter_id' => $item['newsletter_id']));

					if (strtotime($nl->sent_started) < 0) {
						$nl->save(array(
							'sent_started' => date('Y-m-d H:i:s')
						));
					}
					unset($nl);

					// Update the queue item after success mailing
					if ($letter->state) {

						$db->setQuery(
							'UPDATE #__newsletter_queue SET state=0 '
							.' WHERE newsletter_id=' . $item['newsletter_id']
							.' AND subscriber_id=' . $item['subscriber_id']);
						$db->query();
					}

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
					'mailer_cron_last_execution_time' => date('Y-m-d H:i:s')
				));
				$table->store();
			}

			echo json_encode(array(
				'data' => $ret,
				'count' => count($list),
				'error' => ''
			));
		} else {
			echo json_encode(array(
				'error' => 'The interval is not exedeed'
			));
		}
	}

}

