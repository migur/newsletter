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
	
	public static function jsonDecode($json, $assoc = false, $depth = 512)
	{
		//This will convert ASCII/ISO-8859-1 to UTF-8.
		//Be careful with the third parameter (encoding detect list), because
		//if set wrong, some input encodings will get garbled (including UTF-8!)
		$json = mb_convert_encoding($json, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');

		//Remove UTF-8 BOM if present, json_decode() does not like it.
		if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
			$json = substr($json, 3);
		}

		return json_decode($json, $assoc, $depth);
	}
	
	public static function remotePost($url, array $post = NULL, array $options = array()) 
	{ 

		$postArray = $post;
		if (!empty($post)) {
			$postArray = array();
			foreach ($post as $key => $value) {
				ob_start();
				if (is_object($value) || is_array($value)) {
					var_dump($value);
				} elseif (is_string($value)) {
					echo "string '".$value."'";
				} else {
					echo $value;
				}
				
				$postArray[$key] = trim(ob_get_contents(), "\n");
				ob_end_clean();
					
			}
		}
		
		
		$defaults = array( 
			CURLOPT_POST => 1, 
			CURLOPT_HEADER => 0, 
			CURLOPT_URL => $url, 
			CURLOPT_FRESH_CONNECT => 1, 
			CURLOPT_REFERER => $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
			CURLOPT_RETURNTRANSFER => 1, 
			CURLOPT_FORBID_REUSE => 1, 
			CURLOPT_TIMEOUT => 4, 
			CURLOPT_POSTFIELDS => http_build_query($postArray) 
		); 

		$ch = curl_init(); 
		curl_setopt_array($ch, ($options + $defaults)); 
		if( ! $result = curl_exec($ch)) 
		{ 
			trigger_error(curl_error($ch)); 
		} 
		curl_close($ch); 
		return $result; 
	} 
}

/**
 * Legacy support for class name
 * Should be removed after 12.07
 */
class DataHelper extends NewsletterHelperData
{}
