<?php

/**
 * The data managing helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class AclHelper
{
	public static $resultForAbsentAction = true;
	public static $resultForNonsetAction = false;
	
	public static function getActions()
	{	
		jimport('joomla.access.access');
		$result	= array();
 
		$actions = JAccess::getActions('com_newsletter', 'component');
 
		foreach ($actions as $action) {
			$result[] = $action->name;
		}
 
		return $result;
	}
	
	
	/**
	 * Check if user is allowed to do the requested action
	 * Process ONLY local permissions
	 * 
	 * @param type $action
	 * @return type 
	 */
	public static function taskIsAllowed()
	{
		$task = JRequest::getCmd('task');
		$user = JFactory::getUser();
		
		$action = 'com_newsletter.'.$task;
		$actions = self::getActions();
		if (!in_array($action, $actions)) {
			return self::$resultForAbsentAction;
		}
		
		$verdict = $user->authorise($action, 'com_newsletter');
		//var_dump($action, $verdict); die;
		if ($verdict !== null) {
			return $verdict;
		}
	
		return $resultForNonsetAction;
	}
}
