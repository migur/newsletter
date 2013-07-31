<?php

/**
 * The newsltter main component helper
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Process component helper.
 *
 * @since		1.0
 */
class NewsletterHelperProcess
{
	protected static $_timers = array();
	
	/**
	 * Starts the timer with current time.
	 * 
	 * @param type $timerName
	 * @param type $label 
	 */
	public static function startTimer($timerName, $label = 'start')
	{
		self::$_timers[$timerName] = array();
		self::addTimerPoint($timerName, $label);
	}

	
	public static function getTimer($timerName, $options = array())
	{
		$options = array_merge(
			array('dateFormat' => 'Y-m-d H:i:s', 'mode' => 'relative', 'addEndPoint' => true), 
			(array) $options
		);
		
		if ($options['addEndPoint']) {
			self::addTimerPoint($timerName, 'end');
		}
		
		$est = self::_getTimer($timerName);
		
		if($options['mode'] == 'relative') {
			$res = array(); $first = null;
			foreach($est as $item) {
				if (!$first) {
					$res[] = array('time' => date($options['dateFormat'], $item['time']), 'label' => $item['label']);
					$first = $item['time'];
				} else {
					$res[] = array('time' => '+'. ($item['time'] - $first) . 's', 'label' => $item['label']);
				}	
			}
			return $res;
		}
		return $est;
	}


	/**
	 * Add the time point with current time to timer.
	 * 
	 * @param type $timerName
	 * @param type $label 
	 */
	public static function addTimerPoint($timerName, $label = '')
	{
		if (!isset(self::$_timers[$timerName])) return false;
		
		self::$_timers[$timerName][] = array('time' => microtime(true), 'label' => $label);
		
		return true;
	}

	
	protected static function _getTimer($timerName)
	{
		return isset(self::$_timers[$timerName])?
			self::$_timers[$timerName] : array();
	}
	
}
