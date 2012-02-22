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

JLoader::import('tables.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.thread', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.plans.common', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.plans.scheduled', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.plans.eventbased', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.threads.common', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.threads.eventbased', JPATH_COMPONENT_ADMINISTRATOR, '');

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
	public function processSubscription($options) 
	{
		// Check required parameters
		if (empty($options['subscriberId'])) {
			LogHelper::addError('COM_NEWSLETTER_AUTOMAILING_SUBSCRIPTION_SUBSCRIBER_ID_ABSENT', LogHelper::CAT_AUTOMAILING);
			return false;
		}
	
		try {
			
			// Create subscription threads and run it 
			$sid = $options['subscriberId'];
			$plans = $this->getEventbasedPlans('subscription');

			if (!empty($plans)){
				foreach($plans as $plan) {

					$thread = $plan->createThread(array(
						'targets' => array(
							'type' => 'subscriber',
							'ids' => array(
								$sid
					))));

					$thread->run();
				}
			}	
			
		} catch (Exception $e) {
			
			$this->setError($e->getMessage());
			LogHelper::addError('COM_NEWSLETTER_AUTOMAILING_ERROR', LogHelper::CAT_AUTOMAILING, array('Message' => $e->getMessage()));
			return false;
		}
		
		return true;
	}

	
	
	/**
	 * Handle all plans for subscription. 
	 * Main goal - to stop all automailings for unsubscribed user.
	 */
	public function processUnsubscription($options) 
	{
		// Check required parameters
		if (empty($options['subscriberId'])) {
			LogHelper::addError('COM_NEWSLETTER_AUTOMAILING_UNSUBSCRIPTION_SUBSCRIBER_ID_ABSENT', LogHelper::CAT_AUTOMAILING);
			return false;
		}
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
	public function getEventbasedPlans($event = null) {
		
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
			  ->from('#__newsletter_automailings')
			  ->where('automailing_type = "eventbased"');
		
		if (!empty($event)) {
			$query->where('automailing_event = '.$dbo->quote($event));
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
		$query->select('*')
			  ->from('#__newsletter_threads')
			  ->where('type="automail"');
		$dbo->setQuery($query);
		$obj = $dbo->loadObjectList();
		
		$res = array();
		if (!empty($obj)) {
			foreach($obj as $item) {
				 $entity = NewsletterAutomlailingThreadCommon::factory($item->subtype);
				 $entity->bind($item);
				 $entity->paramsFromJson();
				 $res[] = $entity;
			}	
		}
		
		return $res;
	}
	
	public function setError($error){
		$this->_error = $error;
	}
	
	public function getLastError(){
		return $this->_error;
	}
}
