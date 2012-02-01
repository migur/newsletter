<?php

/**
 * The controller for automailing view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerTest extends JControllerForm
{
	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function addFakeUsers()
	{
		$count = JRequest::getInt('count', 100);

		$start = JRequest::getInt('start', 0);

		$dbo = JFactory::getDbo();
		for($i=$start; $i < $start + $count; $i++) {
			$dbo->setQuery('INSERT INTO #__users (name,username,email,password,userType,block,sendEmail,registerDate,lastVisitDate,activation,params) values("zFake User '.$i.'", "zfakeuser'.$i.'", "zfakeuser'.$i.'@gmail.com", "", "", "", 1, "'.date('Y-m-d H:i:s').'", 0, "", "{}")');
			//echo $dbo->getQuery();
			$dbo->query();
		}
		
		die('ok');
	}
	
	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function addFakeSubscribers()
	{
		$count = JRequest::getInt('count', 100);

		$start = JRequest::getInt('start', 0);

		$dbo = JFactory::getDbo();
		for($i=$start; $i < $start + $count; $i++) {
			$dbo->setQuery('INSERT INTO #__newsletter_subscribers (name,email,state,html,user_id,created_on,created_by,modified_on,modified_by,locked_on,locked_by,confirmed,subscription_key,extra) values("zFake Subscriber '.$i.'", "zfakesubscriber'.$i.'@gmail.com", "1", "1", "0", "'.date('Y-m-d H:i:s').'", "0", "0", "0", "0", "0", "1", "0", "{}")');
			//echo $dbo->getQuery();
			$dbo->query();
		}
		
		die('ok');
	}
	
	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function removeFakeSubscribers()
	{
		$dbo = JFactory::getDbo();
		for($i=$start; $i < $start + $count; $i++) {
			$dbo->setQuery('DELETE FROM #__newsletter_subscribers WHERE name LIKE "zFake%"');
			//echo $dbo->getQuery();
			$dbo->query();
		}
		
		die('ok');
	}
	
}

