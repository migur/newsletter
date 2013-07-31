<?php

/**
 * The subscribers list model. Implements the standard functional for subscribers list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.smtpprofile', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('tables.mailboxprofile', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('tables.history', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('tables.thread', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('helpers.mail', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('helpers.log', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('models.automailing.plans.common', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('models.automailing.plans.scheduled', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('models.automailing.plans.eventbased', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('models.automailing.threads.common', COM_NEWSLETTER_PATH_ADMIN);
JLoader::import('models.automailing.threads.eventbased', COM_NEWSLETTER_PATH_ADMIN);

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterAutomailingManager
{
	protected $_error = null;
	
	/**
	 * Handle all plans for subscription. 
	 * Main goal - to start "subscription" automailings for subscriber.
	 * and subscription event.
	 */
	public function processSubscription($sid = null, $lids = array()) 
	{
		// Check required parameters
		if (empty($sid)) {
			NewsletterHelperLog::addError('COM_NEWSLETTER_AUTOMAILING_SUBSCRIPTION_SUBSCRIBER_ID_ABSENT', NewsletterHelperLog::CAT_AUTOMAILING);
			return false;
		}
	
		// Get lists ids
		$lists = !empty($lids)? (array) $lids : array();
		
		if (empty($lists)) {
			//Nothing to process
			return true;
		}
		
		try {
			
			// Create subscription threads but dont run it 
			$plans = $this->getEventbasedPlans('subscription', $lists);
			if (!empty($plans)){
				foreach($plans as $plan) {

					$thread = $plan->createThread(array(
						'targets' => array(
							'type' => 'subscriber',
							'ids' => array(
								$sid
						)),
						'lists' => $lids
					));

					// Cant run there. 
					// This is issue of a cron.
					//$thread->run();
				}
			}	
			
		} catch (Exception $e) {
			
			$this->setError($e->getMessage());
			NewsletterHelperLog::addError('COM_NEWSLETTER_AUTOMAILING_ERROR', NewsletterHelperLog::CAT_AUTOMAILING, array('Message' => $e->getMessage()));
			return false;
		}
		
		return true;
	}

	
	
	/**
	 * Handle all plans for subscription. 
	 * Main goal - to stop all automailings for unsubscribed user.
	 */
	public function processUnsubscription($sid = null, $lids = array()) 
	{
		// Check required parameters
		if (empty($sid)) {
			NewsletterHelperLog::addError('COM_NEWSLETTER_AUTOMAILING_UNSUBSCRIPTION_SUBSCRIBER_ID_ABSENT', NewsletterHelperLog::CAT_AUTOMAILING);
			return false;
		}
		
		$dbo = JFactory::getDbo();
		
		// Delete all threads
		// which have "eventbased - on subscription" automailing as parent 
		// that does not contain any subscriber's list
		$query = 
			'DELETE #__newsletter_threads FROM #__newsletter_threads ' .
			'JOIN (	' .
				'SELECT DISTINCT a.automailing_id, COUNT(sublist_id) AS sublist_id ' .
				'FROM #__newsletter_automailings AS a ' .
				'JOIN #__newsletter_automailing_targets AS atr ON a.automailing_id = atr.automailing_id ' .
				'LEFT JOIN #__newsletter_sub_list AS sl ON (sl.list_id = atr.target_id AND sl.subscriber_id=' . (int) $sid . ') ' .
				'WHERE a.automailing_type = "eventbased" AND a.automailing_event = "subscription" AND atr.target_type = "list" ' .
				'GROUP BY automailing_id HAVING sublist_id = 0 ' .
			') AS a ON #__newsletter_threads.parent_id = a.automailing_id ' .
			'WHERE #__newsletter_threads.target=' . (int) $sid . ' AND #__newsletter_threads.target_type = "subscriber"';

		$dbo->setQuery($query);
		return $dbo->query();
	}

	/**
	 * Handle all plans for subscription. 
	 * Main goal - to stop all automailings for unsubscribed user.
	 */
	public function processSubscriberDeletion($sid) 
	{
		// Check required parameters
		if (empty($sid)) {
			NewsletterHelperLog::addError('COM_NEWSLETTER_AUTOMAILING_DELETE_SUBSCRIBER_ID_ABSENT', NewsletterHelperLog::CAT_AUTOMAILING);
			return false;
		}
		
		$dbo = JFactory::getDbo();
		
		// Delete all threads
		// which have "eventbased - on subscription" automailing as parent 
		// that has deleted subscriber as a target
		$query = 
			'DELETE FROM #__newsletter_threads ' .
			'WHERE ' .
			'target=' . (int) $sid . ' AND ' . 
			'target_type = "subscriber"';

		$dbo->setQuery($query);
		return $dbo->query();
	}

	
	
	/**
	 * Get all scheduled plans.
	 */
	public function getScheduledPlans() {
		
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
			  ->from('#__newsletter_automailings')
			  ->where('automailing_type = "scheduled"');
		$dbo->setQuery($query);
		$obj = $dbo->loadObjectList();
		
		$res = array();
		
		if (!empty($obj)) {
			foreach($obj as $item) {
				$entity = NewsletterAutomlailingPlanCommon::factory($item->automailing_type);
				$entity->bind($item);
				$entity->paramsFromJson();
				$res[] = $entity;
			}	
		}
		
		return $res;
	}

	/**
	 * Get registered plans for necassary event.
	 */
	public function getEventbasedPlans($event = null, $lists = array()) {
		
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('DISTINCT a.*')
			  ->from('#__newsletter_automailings AS a')
			  ->where('automailing_type = "eventbased"');
		
		if (!empty($event)) {
			$query->where('automailing_event = '.$dbo->quote($event));
		}

		if (!empty($lists)) {
			$query->join('LEFT', '#__newsletter_automailing_targets AS t ON a.automailing_id = t.automailing_id');
			$query->where('(a.scope="all" OR ' . 
				'(t.target_type = "list" AND t.target_id IN ("' . implode('","', $lists) . '")))'
			);
		}
		
		$dbo->setQuery($query);
		$obj = $dbo->loadObjectList();
		
		$res = array();
		
		if (!empty($obj)) {
			foreach($obj as $item) {
				$entity = NewsletterAutomlailingPlanCommon::factory($item->automailing_type);
				$entity->bind($item);
				$entity->paramsFromJson();
				$res[] = $entity;
			}	
		}

		return $res;
	}
	
	public function getAutomailingThreads() {
		
		$dbo = JFactory::getDbo();
		
		$query = $dbo->getQuery(true);
		$query->select('DISTINCT t.*')
			  ->from('#__newsletter_threads AS t')
			  ->where('type="automail"');
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	}
	
	public function setError($error){
		$this->_error = $error;
	}
	
	public function getLastError(){
		return $this->_error;
	}
}
