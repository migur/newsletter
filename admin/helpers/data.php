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

class DataHelper
{

	static $importables = array(
		'jusers', // Available since 1.0.4
		'acymailing',
		'ccnewsletter',
		'rsmail',
		'jnews',
		'acajoom',
		'communicator'
	);
	static $managers = array();

	/**
	 * Fetch all data about subscribers and lists from com_newsletter and
	 * converts it to the CSV
	 *
	 * @param  array  - the lists of names of heders
	 *
	 * @return string - the CSV in the string
	 * @since 1.0
	 */
	static function exportListsCSV($headers = null)
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
	static function exportLists()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.email, s.name, COALESCE(l.name, "") AS list_name, s.created_on AS created');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('left', '#__newsletter_sub_list AS sl ON sl.subscriber_id=s.subscriber_id');
		$query->join('left', '#__newsletter_lists AS l ON sl.list_id=l.list_id');

		// Set the query
		//echo $query->__toString(); die();
		$db->setQuery($query);
		$files = $db->loadObjectList();

		return (array) $files;
	}



	/**
	 * Get all supported components and check if they are valid to import
	 *
	 * @return array - array of objects (info about component)
	 */
	public function getSupportedComponents()
	{
		// Fetch all supported component managers
		$res = array();
		foreach (self::$importables as $com) {

			$item = new stdClass();
			$item->type = $com;
			$item->valid = false;
			$item->name = null;

			$man = self::getComponentInstance($com);
			if (is_object($man)) {
				$item->valid = $man->isValid();
				$item->name = $man->getName();
			}
			$res[] = $item;
		}
		return $res;
	}

	/**
	 * Get the component manager instance
	 *
	 * @param  string - the type of a component
	 *
	 * @return object  - an instance of a mananger
	 * @since  1.0
	 */
	public static function getComponentInstance($com)
	{
		if (!empty(self::$managers[$com]) && is_object(self::$managers[$com])) {
			return self::$managers[$com];
		}
		if (!@include_once JPATH_LIBRARIES . DS . 'migur' . DS . 'library' . DS . 'managers' . DS . strtolower($com) . '.php') {
			return false;
		}

		$class = $com . 'manager';
		$man = new $class;
		self::$managers[$com] = $man;
		return self::$managers[$com];
	}

}
