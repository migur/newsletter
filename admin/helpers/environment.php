<?php

/**
 * The environment helper. 
 * Checks various aspects of environment (J!, php, php libs, etc..).
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class EnvironmentHelper 
{
    static $data = array();
    
	/**
	 * Perform checks. Returns verbal messages.
	 * 
	 * @param array|string names of checks you want to perform
	 * 
	 * @return array Verbal warnings
	 * 
	 * @since 1.0.3
	 */
	public static function getWarnings($checkList = array()) 
	{
		$methods = self::getAvailableChecks();
		
		// Get only requested and available
		if (!empty($checkList)) {
			$checkList = (array) $checkList;
			$methods = array_intersect($checkList, $methods);
		}

		// Do checks
		$res = array();
		foreach ($methods as $m) {
            self::$data = array();
			if (!self::$m()) {
				array_push($res, JText::sprintf(
					'COM_NEWSLETTER_ENVIRONMENT_'.strtoupper($m), 
					isset(self::$data[0])? self::$data[0] : '', 
					isset(self::$data[1])? self::$data[1] : ''));
			}
		}
        self::$data = array();

		return $res;
	}

	
	/**
	 * Gets all available checks
	 * 
	 * @return array 
	 */
	public function getAvailableChecks()
	{
		$ref = new ReflectionClass('EnvironmentHelper');
		$methods = $ref->getMethods();

		// Sanitize $methods
		$res = array();
		foreach ($methods as &$m) {

			if (substr($m->getName(), 0, 5) == 'check') {
				array_push($res, $m->getName());
			}
		}
		return $res;
	}
	
	/**
	 * Add warnings to the application
	 * 
	 * @param array|string names of checks you want to perform
	 * 
	 * @since 1.0.3
	 */
	public static function showWarnings($checkList = array()) 
	{
		$app = JFactory::getApplication();
		foreach (self::getWarnings($checkList) as $w) {
			$app->enqueueMessage($w, 'error');
		}
	}

	/**
	 * Check if the J! version desire our component.
	 *
	 * @return boolean
	 * 
	 * @since 1.0.3
	 */
	public static function checkJoomla() {
		return (version_compare(JVERSION, '1.7') >= 0);
	}

	/**
	 * Check if the imap library is installed.
	 *
	 * @return boolean
	 * 
	 * @since 1.0.3
	 */
	public static function checkImap() {
		return
			function_exists('imap_open') &&
			function_exists('imap_timeout') &&
			function_exists('imap_delete') &&
			function_exists('imap_close') &&
			function_exists('imap_last_error') &&
			function_exists('imap_num_msg') &&
			function_exists('imap_fetchstructure') &&
			function_exists('imap_header') &&
			function_exists('imap_fetchheader') &&
			function_exists('imap_body') &&
			function_exists('imap_fetchbody') &&
			function_exists('imap_bodystruct') &&
			function_exists('imap_utf7_decode') &&
			function_exists('imap_getmailboxes');
	}

	
	
	/**
	 * Check the ability to write into /logs if admin turn on debug
	 *  
	 */
	public static function checkLogs() 
	{
		$params = JComponentHelper::getParams('com_newsletter');
		$logging = (int)$params->get('debug', '0');
		return is_writable(JPATH_ROOT . '/logs') || empty($logging);
	}

	
	
	public static function checkUserConflicts()
	{
		$conflictsCount = self::getConflictsCount();
		self::$data[0] = $conflictsCount;
		self::$data[1] = 
			'<a id="conflict-resolver-link" '.
			'href="'.JRoute::_('index.php?option=com_newsletter&view=conflicts', false).'">'.
			JText::_('COM_NEWSLETTER_HERE').
			'</a>';
		return $conflictsCount == 0;
	}


	
	public static function checkAcl()
	{
		$asset = JTable::getInstance('asset');
		return $asset->loadByName('com_newsletter');
	}
	
	
	
	public static function getConflictsCount()
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery(
			'SELECT COUNT(*) AS cnt '.
			'FROM #__users AS u '.
			'JOIN #__newsletter_subscribers AS s ON u.email = s.email '.
			'WHERE u.id != s.user_id');
		$res = $dbo->loadAssoc();
		return $res['cnt'];
	}
	
	
	
	public static function getLastSchema()
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->select('version_id')
			->from('#__schemas AS s')
			->join('','#__extensions AS e ON s.extension_id=e.extension_id')
			->where('e.element="com_newsletter"');
		
		$dbo->setQuery($query);
		$res = $dbo->loadAssoc();
		return $res['version_id'];
	}
}
