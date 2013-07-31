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

JLoader::import('models.automailing.threads.scheduled', COM_NEWSLETTER_PATH_ADMIN, '');


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
		// Check if this plan has no exwecutions in the past
		if ($this->hasExecutedOnceBefore()) {
			return false;
		}
		
		/* No threads has been created yet for this single-threated plan
		   Check the date */
		$series = $this->getSeries();
		
		if (count($series) == 0 || time() < $series[0]->time_start) {
			return false;
		}
			
		/** Create thread and start it! */
		$thread = $this->createThread();
		
		/** Change the status of a plan
		 Increment the counter of executions */
		if (empty($this->params['execsCount'])) {
			$this->params['execsCount'] = 0;
		}
		$this->params['execsCount']++;
		
		$this->automailing_state = 1;
		$this->save(array());
		
		return true;
	}
	
	/**
	 * Creates the thread on basis of $this plan
	 * 
	 * @return NewsletterAutomlailingThreadCommon 
	 */
	public function createThread($options = array())
	{
		// Get all targets
		$tergetsModel = MigurModel::getInstance('AutomailingTargets', 'NewsletterModel');
		$targets = $tergetsModel->findByAid($this->automailing_id);

		// Get their ids
		$targetIds = array();
		foreach($targets as $target){
			$targetIds[] = $target->target_id;
		}
		
		// Creates new thread
		$thread = new NewsletterAutomlailingThreadScheduled();
		$thread->save(array(
			'parent_id' => $this->automailing_id,
			'type'      => 'automail',
			'subtype'   => 'scheduled',
			'resource'  => null,
			'target_type' => $targets[0]->target_type,
			'params'    => array(
				'step'      => 0, 
				'timeCreated' => time())
		));
		
		return $thread;
	}
}
