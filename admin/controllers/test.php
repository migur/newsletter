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
	 * Used only for development.
	 *
	 * @return void
	 * @since 1.0
	 */
	function addFakeUsers()
	{
		$count = JRequest::getInt('count', 10000);

		$start = JRequest::getInt('start', 0);

		$prefix = JRequest::getString('prefix', 'ZZ Test user');
		
		$dbo = JFactory::getDbo();
		
		$dbo->setQuery('SET foreign_key_checks=0');
		$dbo->query();
		
		$dbo->transactionStart();

		for($i=$start; $i < $start + $count; $i++) {
			$name = str_replace(' ', '_', $prefix.$i);
			$username = $prefix.$i;
			$email = str_replace(' ', '.', strtolower($prefix).$i.'@absentdomain.com');
			$dbo->setQuery('INSERT INTO #__users (name,username,email,password,userType,block,sendEmail,registerDate,lastVisitDate,activation,params) values("'.$name.'", "'.$username.'", "'.$email.'", "", "", "", 1, "'.date('Y-m-d H:i:s').'", 0, "", "{}")');
			//echo $dbo->getQuery();
			$res = $dbo->query();
			echo "\n".$name.' - '.($res?'ok':'fail');
		}
		
		$dbo->transactionCommit();
		
		$dbo->setQuery('SET foreign_key_checks=1');
		$dbo->query();
		
		die("\n Complete");
	}
	
	/**
	 * Used only for development.
	 *
	 * @return void
	 * @since 1.0
	 */
	function addFakeSubscribers()
	{
		$count = JRequest::getInt('count', 100);

		$start = JRequest::getInt('start', 0);

		$prefix = JRequest::getString('prefix', 'ZZ Test subscriber');
		
		$dbo = JFactory::getDbo();
		
		$dbo->setQuery('SET foreign_key_checks=0;');
		$dbo->query();

		$dbo->transactionStart();
		
		for($i=$start; $i < $start + $count; $i++) {
			$name = $prefix.$i;
			$email = str_replace(' ', '.', strtolower($prefix).$i.'@absentdomain.com');
			$dbo->setQuery('INSERT INTO #__newsletter_subscribers (name,email,state,html,user_id,created_on,created_by,modified_on,modified_by,locked_on,locked_by,confirmed,subscription_key,extra) values("'.$name.'", "'.$email.'", "1", "1", "0", "'.date('Y-m-d H:i:s').'", "0", "0", "0", "0", "0", "1", "0", "{}")');
			//echo $dbo->getQuery();
			$res = $dbo->query();
			echo "\n".$name.' - '.($res?'ok':'fail');
		}
		
		$dbo->transactionCommit();

		$dbo->setQuery('SET foreign_key_checks=1;');
		$dbo->query();

		die("\n Complete");
	}

	/**
	 * Used only for development.
	 *
	 * @return void
	 * @since 1.0
	 */
	function createImportCSV()
	{
		$count = JRequest::getInt('count', 10000);
		$start = JRequest::getInt('start', 0);

		$prefix = JRequest::getString('prefix', 'ZZ Test imported');
		$q = JRequest::getString('quote', '"');
		$s = JRequest::getString('separator', ',');
		
		$csv = '';
		
		$csv .= 
			$q.'Name'.$q .$s. 
			$q.'Email'.$q  .$s.
			$q.'Html'.$q  ."\n";
			
			
		for($i=$start; $i < $start + $count; $i++) {
			$name = $prefix.$i;
			$email = str_replace(' ', '.', strtolower($prefix).$i.'@absentdomain.com');
			$csv .= 
				$q.$name.$q .$s.
				$q.$email.$q .$s.
				$q.rand(0,1).$q
				."\n";
		}

		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Length: " . strlen($csv));
		header("Content-Disposition: attachment; filename=test-import-list-" . date('Y-m-d-H-i-s') . '.csv');
		echo $csv;
		die;
	}
	

	/**
	 * Used only for development.
	 *
	 * @return void
	 * @since 1.0
	 */
	function confirmSubscribers()
	{
		$listId = JRequest::getInt('list_id');

		if (empty($listId)) {
			die("\n No list id");
		}	
		
		$dbo = JFactory::getDbo();
		
		$dbo->setQuery('UPDATE #__newsletter_sub_list SET confirmed=1 WHERE list_id = '.$listId);
		$res = $dbo->query();

		die("\n Complete");
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
		$dbo->setQuery('DELETE FROM #__newsletter_subscribers WHERE name LIKE "zFake%"');
		//echo $dbo->getQuery();
		$dbo->query();
		
		die("\n Complete");
	}

	/**
	 * Turn on/off dry run mode
	 *
	 * @return void
	 * @since 1.0
	 */
	function setDryrun()
	{
		$mode = JRequest::getInt('mode');
		
		if ($mode === null) {
			die("\n Mode is absent");
		}
		
		NewsletterHelperNewsletter::setParam('dryrun_mailing', (int) $mode);
		
		die("\n Complete");
	}
}

