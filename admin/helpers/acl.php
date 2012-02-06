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
	/* No need to parse rules again and again */
	protected static $_toArrayCache = null;
	
	/* Default results */
	public static $resultForAbsentAction = true;
	public static $resultForNonsetAction = false;
	
	/* This array allows to map requested action
	 * to Joomla standard rule names.
	 * Subarray are the list of permissions that 
	 * user MUST have all to perform current action.  */
	public static $actionToRuleMap = array(
		'create'    => array('create'),
		'add'       => array('create'),
		'edit'      => array('edit'),
		'publish'   => array('edit.state'),
		'unpublish' => array('edit.state'),
		'delete'    => array('delete'),
		'remove'    => array('delete'),
		'save2copy' => array('create', 'edit'),
		'view'      => array('edit')
	);


	
	/**
	 * Get list of all actions described in access.xml
	 * 
	 * @return array of strings
	 */
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
	 * Now process ONLY local permissions.
	 * Hope global will be added soon.
	 * 
	 * @param type $action
	 * @return type 
	 */
	public static function actionIsAllowed($action)
	{
		$user = JFactory::getUser();
		
		// action to array
		$actionPath = explode('.', $action);
		$actionPath = array_reverse($actionPath);
		
		// If no action provided
		if (empty($actionPath)) {
			return false;
		}

		// First need to check the global permission
		// Lets get the list of permissions that user must have 
		// to perform current action
		if (!in_array($actionPath[0], (array_keys(self::$actionToRuleMap)))) {
			return false;
		}
		$rules = self::$actionToRuleMap[$actionPath[0]];
		
		// Check global and custom permissions permissions.
		// If any one permission is DENIED then it is the end...
		$customRules = self::toArray();
		foreach($rules as $rule) {
			
			// Global... Not now hope coming soon...
//			if ($user->authorise('core.'.$rule) === false) {
//				return false;
//			};
			
			// Custom...
			$rulePath = $actionPath;
			$rulePath[0] = $rule;
			$customRule = 'com_newsletter.'. implode('.', array_reverse($rulePath));
			
			if (
				(!in_array($customRule, array_keys($customRules)) &&
				!self::$resultForAbsentAction) || !$customRules[$customRule]
			) {
				return false;
			}
		}
		
		return true;
	}

	
	
	/**
	 * Check wether curent user has permission to access the component 
	 * 
	 * @return true if he has
	 */
	public static function canAccessComponent()
	{
		return (bool)JFactory::getUser()->authorise('core.manage', 'com_newsletter'); 
	}
	

	
	/**
	 * Check wether curent user has permission to configure the component 
	 * 
	 * @return true if he has
	 */
	public static function canConfigureComponent()
	{
		return (bool)JFactory::getUser()->authorise('core.admin', 'com_newsletter'); 
	}

	
	
	/**
	 * Set parameters to process ACCESS DENIED page
	 * 
	 * @param type $warning 
	 */
	public static function redirectToAccessDenied($warning = true)
	{
		if ($warning) {
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}	
		
		if (JRequest::getString('view') != 'error' ||  JRequest::getString('layout') != 'denied') {
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_newsletter&view=error&layout=denied', false));
		}	
	}
	
	public static function toArray()
	{
		if(self::$_toArrayCache !== null) {
			return self::$_toArrayCache;
		}
		
		$user = JFactory::getUser();
		
		$result = array();
		$actions = self::getActions();
		foreach($actions as $action) {
			$access = $user->authorise($action, 'com_newsletter');
			$result[$action] = ($access!==null)? $access : self::$resultForNonsetAction;
		}
		
		self::$_toArrayCache = $result;
		
		return self::$_toArrayCache;
	}
	
}
