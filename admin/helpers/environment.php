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

class EnvironmentHelper {

	static public $warnings = array(
		'checkJoomla' => 'You have old version of Joomla. Please update.',
		'checkImap' => 'Imap library is unavailable. Please install.'
	);

	/**
	 * Perform checks. Returns verbal messages.
	 * 
	 * @param array|string names of checks you want to perform
	 * 
	 * @return array Verbal warnings
	 * 
	 * @since 1.0.3
	 */
	public static function getWarnings($checkList = array()) {

		$ref = new ReflectionClass('EnvironmentHelper');
		$methods = $ref->getMethods();

		// Sanitize $methods
		$res = array();
		foreach ($methods as &$m) {

			if (substr($m->getName(), 0, 5) == 'check') {
				array_push($res, $m->getName());
			}
		}
		$methods = $res;

		// Get only requested and available
		if (!empty($checkList)) {
			$checkList = (array) $checkList;
			$methods = array_intersect($checkList, $methods);
		}

		// Do checks
		$res = array();
		foreach ($methods as $m) {
			if (!self::$m()) {
				array_push($res, JText::_(self::$warnings[$m]));
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
	public static function showWarnings($checkList = array()) {
		
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
		return (version_compare(JVERSION, '1.7') > 0);
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

}
