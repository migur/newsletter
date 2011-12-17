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
class NewsletterAutomlailingPlanCommon extends MigurJTable
{
	public $_threads = null;
	
	public $_series = null;
	
	
	public function __construct($data = array()) {

		parent::__construct('#__newsletter_automailings', 'automailing_id', JFactory::getDbo());
		
		if (!empty($data)) {
			foreach($data as $key => $value) {
				$this->$key = $value;
			}
		}	
	}


	public function start() {
		throw new Exception('Method Start need to be implemented in child class');
	}

	
	public function createThread() {
		throw new Exception('Method createThread need to be implemented in child class');
	}
	

	/**
	 * Give information if this plan has been executed once before
	 */
	public function hasExecutedOnceBefore()
	{
		return !empty($this->params['execsCount']);
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
		$query = $dbo->getQuery(true);
		$query->select('*')
			  ->from('#__newsletter_automailing_items')
			  ->where('automailing_id = '.(int)$this->automailing_id);
		$dbo->setQuery($query);
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
	public static function factory($type)
	{
		// Decide what object we should create
		if ($type == 'scheduled') {
			JLoader::import('models.automailing.plans.scheduled', JPATH_COMPONENT_ADMINISTRATOR, '');
			return new NewsletterAutomlailingPlanScheduled();
		}

		if ($type == 'eventbased') {
			JLoader::import('models.automailing.plans.eventbased', JPATH_COMPONENT_ADMINISTRATOR, '');
			return new NewsletterAutomlailingPlanEventbased();
		}
		
		throw new Exception('Unallowed type of plan instance:'.$type);
	}
}
