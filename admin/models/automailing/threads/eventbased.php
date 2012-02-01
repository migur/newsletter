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
class NewsletterAutomlailingThreadEventbased extends NewsletterAutomlailingThreadCommon
{
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
		
		// The start date of a thread is the moment of subscription.
		
		$basetime = $this->params['timeCreated'];
		foreach($list as &$item) {
			$item->time_absolute = $basetime + $item->time_offset;
		}
		
		$this->_series = $list;
		return $this->_series;
	}
	
	
}
