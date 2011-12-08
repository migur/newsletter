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
JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.plans.common', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.threads.common', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterAutomailingManager
{
	/**
	 * Get all registered plans.
	 */
	public function getScheduledPlans() {
		
		$dbo = JFactory::getDbo();
		$query = 
			"SELECT * FROM #__newsletter_automailings AS a ".
			"WHERE a.automailing_type = ".$dbo->quote('scheduled');

		$dbo->setQuery($query);
		$dbo->query();
		$obj = $dbo->loadObjectList();
		
		$res = array();
		
		if (!empty($obj)) {
			foreach($obj as $item) {
				$res[] = NewsletterAutomlailingPlanCommon::factory($item);
			}	
		
		}
		
		return $res;
	}
	
	public function getAllThreads() {
		
		$dbo = JFactory::getDbo();
		$query = "SELECT * FROM #__newsletter_automailings_threads";

		$dbo->setQuery($query);
		$dbo->query();
		$obj = $dbo->loadObjectList();
		
		$res = array();
		
		if (!empty($obj)) {
			foreach($obj as $item) {
				$res[] = new NewsletterAutomlailingThreadCommon($item);
			}	
		
		}
		
		return $res;
	}
}
