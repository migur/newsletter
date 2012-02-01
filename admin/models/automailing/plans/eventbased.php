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
class NewsletterAutomlailingPlanEventbased extends NewsletterAutomlailingPlanCommon
{
	/**
	 * Do a check and starts new thread.
	 * Only one thread available
	 * 
	 * @return boolean true on start, false otherwise
	 */
	public function start()
	{
		/** Create thread and start it! */
		$thread = $this->createThread();

		/** Change the status of a plan
		 Increment the counter of executions */
		$this->params['execsCount']++;
		$this->automailing_state = 1;
		$this->store();
		
		return true;
	}
	
	/**
	 * Creates the thread on basis of $this plan
	 * 
	 * @return NewsletterAutomlailingThreadCommon 
	 */
	public function createThread($options)
	{
		if (empty($options['targets']['type']) || empty($options['targets']['ids'])) {
			throw new Exception('Cant create chread. Type or id is empty.');
		} 

		// Creates new thread
		$thread = new NewsletterAutomlailingThreadEventbased();
		$thread->save(array(
			'parent_id' => $this->automailing_id,
			'type'      => 'automail',
			'subtype'   => 'eventbased',
			'resource'  => null,
			'params'    => array(
				'step'      => 0,
				'timeCreated' => mktime(),
				'targets'   => array(
					'type' => $options['targets']['type'],
					'ids'  => $options['targets']['ids']
				)
		)));
		
		return $thread;
	}
}
