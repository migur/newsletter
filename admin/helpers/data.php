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

JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterHelperData
{
	/**
	 * Fetch all data about subscribers and lists from com_newsletter and
	 * converts it to the CSV
	 *
	 * @param  array  - the lists of names of heders
	 *
	 * @return string - the CSV in the string
	 * @since 1.0
	 */
	public static function exportListsCSV($headers = null)
	{
		if (empty($heders)) {
			$headers = array(
				'email',
				'name',
				'listname',
				'created'
			);
		}

		// Get the data
		$data = self::exportLists();

		// Create header
		$res[] = '"' . implode('","', $headers) . '"';

		// Create body
		if (is_array($data))
			foreach ($data as &$item) {
				$res[] = '"' . implode('","', array_map('addslashes', (array) $item)) . '"';
			}

		return implode("\n", $res);
	}

	
	/**
	 * Fetch all data about subscribers and lists from com_newsletter
	 *
	 * @return array - the array of the objects(subscriber - list)
	 * @since 1.0
	 */
	public static function exportLists()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.email, s.name, COALESCE(l.name, "") AS list_name, s.created_on AS created');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('left', '#__newsletter_sub_list AS sl ON sl.subscriber_id=s.subscriber_id');
		$query->join('left', '#__newsletter_lists AS l ON sl.list_id=l.list_id');

		// Set the query
		$db->setQuery($query);
		$files = $db->loadObjectList();

		return (array) $files;
	}


	/**
	 * Converts each element of array to int
	 */
	public static function toArrayOfInts($data) 
	{
		$data = (array)$data;
		
		foreach($data as &$item) {
			$item = (int)$item;
		}
		
		return $data;
	}
	
	
	public static function timeIntervaltoVerbal($seconds, $items = array()) 
	{
		if (empty($items)) {
			$items = array('weeks', 'days', 'hours', 'immediately');
		}

		if ($seconds == 0 && in_array('immediately', $items)) {
			return JText::_('COM_NEWSLETTER_IMMEDIATELY');
		}
		
		$data = self::timeIntervalExplode($seconds);
		
		$weekCnt = $data['weeks'];
		$dayCnt  = $data['days'];
		$hourCnt = $data['hours'];
		$minCnt  = $data['minutes'];
		$seconds = $data['seconds'];		
		
		if ($weekCnt > 4) {
			$week = 7;
			$dayCnt += $weekCnt * $week;
			$weekCnt = 0;
		}
		
		$res = '';
		foreach($items as $item) {
			
			switch($item) {
				
				case 'weeks':
					if (!empty($weekCnt)) {
						$res .= ' '.$weekCnt.' '.(($weekCnt == 1)? JText::_('COM_NEWSLETTER_WEEK') : JText::_('COM_NEWSLETTER_WEEKS'));
					}	
					break;

				case 'days':
					if (!empty($dayCnt)) {
						$res .= ' '.$dayCnt.' '.(($dayCnt == 1)? JText::_('COM_NEWSLETTER_DAY') : JText::_('COM_NEWSLETTER_DAYS'));
					}	
					break;

				case 'hours':
					if (!empty($hourCnt)) {
						$res .= ' '.$hourCnt.' '.(($hourCnt == 1)? JText::_('COM_NEWSLETTER_HOUR') : JText::_('COM_NEWSLETTER_HOURS'));
					}	
					break;
					
				case 'minutes':
					if (!empty($minCnt)) {
						$res .= ' '.$minCnt.' '.(($minCnt == 1)? JText::_('COM_NEWSLETTER_MINUTE') : JText::_('COM_NEWSLETTER_MINUTES'));
					}	
					break;
					
				case 'seconds':
					if (!empty($seconds)) {
						$res .= ' '.$seconds.' '.(($seconds == 1)? JText::_('COM_NEWSLETTER_SECOND') : JText::_('COM_NEWSLETTER_SECONDS'));
					}	
					break;
			}
		}
		
		return trim($res);
	}
	
	/**
	 * 
	 * 
	 * @param integer $seconds Interval in seconds to explode
	 * @return array (weeks, days, hours, minutes, seconds) 
	 */
	public static function timeIntervalExplode($seconds)
	{
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$week = $day * 7;
		
		$weekCnt = floor($seconds / $week);
		$seconds -= $weekCnt * $week; 

		$dayCnt = floor($seconds / $day);
		$seconds -= $dayCnt * $day; 

		$hourCnt = floor($seconds / $hour);
		$seconds -= $hourCnt * $hour; 
		
		$minCnt = floor($seconds / $minute);
		$seconds -= $minCnt * $minute; 
		
		return array(
			'weeks' => $weekCnt, 
			'days'  => $dayCnt, 
			'hours' => $hourCnt,
			'minutes' => $minCnt,
			'seconds' => $seconds
		);
	}
	
	/**
	 * Get data from specified column of each element of array
	 * 
	 * @param type $array
	 * @param type $colName 
	 */
	public static function getColumnData($array, $colName)
	{
		$res = array();
		foreach($array as $item) {
			
			if (is_array($item)) {
				
				$res[] = $item[$colName];
				
			} elseif (is_object($item)) {
				
				$res[] = $item->$colName;
				
			} else {
				
				return false;
			}	
		}
		
		return $res;
	}
	
	public static function getDefault($value, $category = '')
	{
		require_once COM_NEWSLETTER_PATH_ADMIN . DIRECTORY_SEPARATOR . 'constants.php';
		$name = strtoupper($category) . '_' . strtoupper($value) . '_DEFAULT';
		
		return defined($name)? constant($name) : null;
	}
}

/**
 * Legacy support for class name
 * Should be removed after 12.07
 */
class DataHelper extends NewsletterHelperData
{}
