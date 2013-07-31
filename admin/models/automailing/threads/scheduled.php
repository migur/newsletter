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

JLoader::import('models.automailing.threads.common', COM_NEWSLETTER_PATH_ADMIN, '');

/**
 * Common methods
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterAutomlailingThreadScheduled extends NewsletterAutomlailingThreadCommon
{
	/**
	 * Get targets of automailing
	 * 
	 * @return list of std objects
	 */
	public function getTargets()
	{
		$dbo = JFactory::getDbo();
		$query = 
			"SELECT * FROM #__newsletter_automailing_targets AS a ".
			"WHERE a.automailing_id = ".(int)$this->parent_id;

		$dbo->setQuery($query);
		$list = $dbo->loadObjectList();
		
		$res = array();
		foreach($list as &$item) {
			$res[] = array('id' => $item->target_id, 'type' => $item->target_type);
		}
		
		return $res;
	}
}
