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

JLoader::import('models.automailing.threads.common', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Common methods
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterAutomlailingPlanCommon extends JTable
{
	public $_threads = null;
	
	public $_series = null;
	
	public function __construct($data = array()) {

		parent::__construct('#__newsletter_automailings', 'automailing_id', JFactory::getDbo());
		
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
	}


	public function start() {
		throw new Exception('Need to be implemented in child class');
	}

	
	/**
	 * Creates the thread on basis of $this plan
	 * 
	 * @param object $data
	 * 
	 * @return NewsletterAutomlailingThreadCommon 
	 */
	public function createThread($data){
		
		$data = (object)$data;
		
		$thread = new NewsletterAutomlailingThreadCommon();
		$thread->save(array(
			'automailing_id' => $this->automailing_id,
			'target_id' => !empty($data->targetId)? $data->targetId : null,
			'target_type' => !empty($data->targetType)? $data->targetType : null,
			'step' => 0
		));
		
		array_push($this->_threads, $thread);
		
		return $thread;
	}
	
	/**
	 * Get all threads for $this plan
	 * 
	 * @return array of NewsletterAutomlailingThreadCommon 
	 */
	public function getThreads() 
	{
		if ($this->_threads !== null) {
			return $this->_threads;
		}
		
		$dbo = JFactory::getDbo();
		$query = 
			"SELECT * FROM #__newsletter_automailings_threads ".
			'where automailing_id='.(int)$this->automailing_id;
		
		$dbo->setQuery($query);
		$dbo->query();
		
		$this->_threads = $dbo->loadObjectList();
		
		return $this->_threads;
	}

	
	/**
	 * Get all items of a plan's series
	 * 
	 * @return list of std objects
	 */
	public function getSeries() {
		
		if ($this->_series !== null) {
			return $this->_series;
		}
		
		$dbo = JFactory::getDbo();
		$query = 
			"SELECT * FROM #__newsletter_automailings_series AS a ".
			"WHERE a.automailing_id = ".(int)$this->automailing_id;

		$dbo->setQuery($query);
		$dbo->query();
		$this->_series = $dbo->loadObjectList();
		
		return $this->_series;
	}
	
	/**
	 * Factory for a instances of NewsletterAutomlailingPlanCommon
	 * 
	 * @param type $data
	 * 
	 * @return instance of NewsletterAutomlailingPlanCommon
	 */
	public static function factory($data)
	{
		// Decide what object we should create
		if ($data->automailing_type == 'scheduled') {
			JLoader::import('models.automailing.plans.scheduled', JPATH_COMPONENT_ADMINISTRATOR, '');
			//include_once __FILE__.DS.'scheduled.php';
			return new NewsletterAutomlailingPlanScheduled($data);
		}

		if ($data->automailing_type == 'eventbased') {
			JLoader::import('models.automailing.plans.eventbased', JPATH_COMPONENT_ADMINISTRATOR, '');
			//include_once __FILE__.DS.'eventbased.php';
			$entity = new NewsletterAutomlailingPlanEventbased($data);
		}
		
		throw new Exception('Unallowed type of instance:'.$data->automailing_type);
	}
}
