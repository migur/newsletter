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
class NewsletterAutomlailingPlanScheduled extends NewsletterAutomlailingPlanCommon
{
	/**
	 * Do a check and starts new thread.
	 * Only one thread available
	 * 
	 * @return boolean true on start, false otherwise
	 */
	public function start()
	{
		/** No threads has been created yet for this single-threated plan */
		if (count($this->getThreads()) > 0) {
			return false;
		}

		/** Check the date */
		$series = $this->getSeries();
		
		if (count($series) == 0 || mktime() < $series[0]->time_start) {
			return false;
		}
			
		/** Create thread and start it! */
		$thread = $this->createThread(array());

		/** Change the status of a plan */
		$this->save(array('automailing_state' => 1));
		return true;
	}
}
