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
class NewsletterAutomlailingThreadCommon extends JTable
{
	public $_series = null;
	
	public function __construct($data = array()) {
		
		$data = (object)$data;
		
		parent::__construct('#__newsletter_automailings_threads', 'thread_id', JFactory::getDbo());
		
		/** Convert target Ids from json */
		if (!empty($data->target_ids)) {
			$data->target_ids = json_decode($data->target_ids);
		}
		
		/** Populate the entity */
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
		
		$this->step = (int)$this->step;
	}

	public function run()
	{
		$series = $this->getSeries();
		
		for(; $this->step < count($series); $this->step++) {
			
			if (false == $this->process($series[$this->step])) {
				break;
			}
		}
		
		/** Check if this is a last step */
		if ($this->step >= count($series)) {
			return $this->destroy();
		}
		
		/** Otherwise increase the step and fall asleep */
		return $this->save(array('step' => $this->step));
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
			"SELECT * FROM #__newsletter_automailings_series AS a ".
			"WHERE a.automailing_id = ".(int)$this->automailing_id." ".
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

		foreach($this->target_ids as $id) {

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
			}
		}
		
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
		if ($this->target_type == 'subscriber') {
			return array($id);
		}
		
		if ($this->target_type == 'list') {
			
			$dbo = JFactory::getDbo();
			$query = 
				"SELECT subscriber_id FROM #__newsletter_lists AS l ".
				"JOIN #__newsletter_sub_lists AS sl ON l.list_id = sl.list_id";
			
			$dbo->setQUery($query);
			$dbo->query();
			return $dbo->loadAssocList(null, 'subscriber_id');
		}
	}
}
