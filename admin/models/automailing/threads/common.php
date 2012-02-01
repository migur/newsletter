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

/**
 * Common methods
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterAutomlailingThreadCommon extends MigurJTable
{
	public $_series = null;
	
	public function __construct($data = array()) {
		
		$data = (array)$data;
		
		parent::__construct('#__newsletter_threads', 'thread_id', JFactory::getDbo());
		
		/** Populate the entity */
		foreach($data as $key => &$value) {
			$this->$key = $value;
		}
	}

	public function run()
	{
		$series = $this->getSeries();
		
		for(; $this->params['step'] < count($series); $this->params['step']++) {
			
			if (false == $this->process($series[$this->params['step']])) {
				break;
			}
		}

		/** Check if this is a last step */
		if ($this->params['step'] >= count($series)) {
			return $this->destroy();
		}
		
		/** Otherwise increase the step and fall asleep */
		return $this->store();
	}
	
	/**
	 * Delete $this thread from DB.
	 */
	public function destroy(){
		
		/** Delete form table*/
		return $this->delete();
	}

	
	/**
	 * Get all items of a plan's series
	 * 
	 * @return list of std objects
	 */
	public function getSeries() 
	{
		if ($this->_series !== null) {
			return $this->_series;
		}
		
		$dbo = JFactory::getDbo();
		$query = 
			"SELECT * FROM #__newsletter_automailing_items AS a ".
			"WHERE a.automailing_id = ".(int)$this->parent_id." ".
			"ORDER BY time_offset";

		$dbo->setQuery($query);
		$dbo->query();
		$list = $dbo->loadObjectList();
		
		$basetime = strtotime($list[0]->time_start);
		foreach($list as &$item) {
			$item->time_absolute = $basetime + $item->time_offset;
		}
		
		$this->_series = $list;
		return $this->_series;
	}
	
	
	/**
	 * Add new elements in queue according to a
	 * @param type $serie 
	 */
	public function process($serie)
	{
		if (mktime() < $serie->time_absolute) {
			return false;
		}
		
		$sents = 0;
		foreach($this->params['targets']['ids'] as $id) {

			$list = $this->_getTargetItems($id);
			foreach($list as $sid) {
			
				$queue = JTable::getInstance('Queue', 'NewsletterTable');
				$queue->save(array(
					'newsletter_id' => $serie->newsletter_id,
					'subscriber_id' => $sid,
					'list_id'       => 0,
					'created'       => date('Y-m-d H:i:s'),
					'state'			=> 1
				));
				unset($queue);
				
				$sents++;
			}
		}
		
		// Update the count of sent items and state of Item
		$serieModel = JModel::getInstance('AutomailingItem', 'NewsletterModel');
		$serie->sent += $sents;
		$serie->status++;
		$serieModel->save((array)$serie);
		
		return true;
	}
	
	
	/**
	 * Get list of subscriber ids depends on type of a target
	 * 
	 * @param int $id
	 * 
	 * @return array list of subscriber ids
	 */
	public function _getTargetItems($id)
	{
		if ($this->params['targets']['type'] == 'subscriber') {
			return array($id);
		}
		
		if ($this->params['targets']['type'] == 'list') {
			
			$dbo = JFactory::getDbo();
			$query = $dbo->getQuery(true);
			$query->select('subscriber_id')
				  ->from('#__newsletter_sub_list')
				  ->where('list_id = '.(int)$id);
			$dbo->setQUery($query);
			return $dbo->loadAssocList(null, 'subscriber_id');
		}
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
			JLoader::import('models.automailing.threads.scheduled', JPATH_COMPONENT_ADMINISTRATOR, '');
			return new NewsletterAutomlailingThreadScheduled();
		}

		if ($type == 'eventbased') {
			JLoader::import('models.automailing.threads.eventbased', JPATH_COMPONENT_ADMINISTRATOR, '');
			return new NewsletterAutomlailingThreadEventbased();
		}
		
		throw new Exception('Unallowed type of thread instance:'.$type);
	}
	
}
