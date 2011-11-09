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
	 * Imports the data about subscribers and lists into com_newsletter
	 *
	 * @param  array - the array of the objects(subscriber - list)
	 *
	 * @return boolean - true on success
	 * @since  1.0
	 */
	public function importLists($list)
	{
		$lists = array();
		$subs = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		foreach ($list as $obj) {

			$lists[$obj->list_name] = 0;
			$subs[$obj->email] = $obj;

			// Add new subscribers with not existing emails
			if (!empty($obj->email)) {
				$query = $db->getQuery(true);
				$query->select('subscriber_id, email');
				$query->from('#__newsletter_subscribers');
				$query->where('email = "' . addslashes(stripslashes($obj->email)) . '"');
				$db->setQuery($query);
				$res = $db->loadObject();

				if (!empty($res)) {
					$subId = $res->subscriber_id;
				} else {

					$db->setQuery(
						'INSERT IGNORE INTO `#__newsletter_subscribers` ' .
						'SET email = "' . addslashes(stripslashes($obj->email)) . '", ' .
						'name = "' . addslashes(stripslashes($obj->name)) . '", ' .
						'created_on = "' . addslashes(stripslashes($obj->created)) . '", ' .
						'user_id = 0, ' .
						'confirmed = 1, ' .
						'subscription_key = 0');
					$db->query();
					$subId = $db->insertId();
					SubscriberHelper::setSubscriptionKey($subId);
				}
			}

			// Create non-exist list.
			if (!empty($obj->list_name)) {
				$query = $db->getQuery(true);
				$query->select('list_id, name');
				$query->from('#__newsletter_lists');
				$query->where('name = "' . addslashes(stripslashes($obj->list_name)) . '"');
				$db->setQuery($query);
				$res = $db->loadObject();
				if (!empty($res)) {
					$listId = $res->list_id;
				} else {
					$db->setQuery(
						'INSERT IGNORE INTO `#__newsletter_lists` ' .
						'SET name = "' . addslashes(stripslashes($obj->list_name)) . '", ' .
						'created_on = "' . date('Y-m-d H:i:s') . '"');

					$db->query();
					$listId = $db->insertId();
				}
			}

			// Join user only if the $subId and $listId are present.
			if (!empty($subId) && !empty($listId)) {

				$query = $db->getQuery(true);
				$query->select('list_id, subscriber_id');
				$query->from('#__newsletter_sub_list');
				$query->where('list_id = ' . $listId);
				$query->where('subscriber_id = ' . $subId);
				$db->setQuery($query);
				$res = $db->loadObject();

				if (empty($res)) {
					$query = 'INSERT IGNORE INTO `#__newsletter_sub_list` ' .
						'SET list_id = ' . $listId . ', ' .
						'subscriber_id = ' . $subId;
					$db->setQuery($query);
					$db->query();
				}
			}
		}

		return true;
	}

	/**
	 * Get all supported components and check if they are valid to import
	 *
	 * @return array - array of objects (info about component)
	 */
	function getSupportedComponents()
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
	public function getComponentInstance($com)
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

	/**
	 * Fetch data from the component via component manager.
	 * The type of a data determines the $type (only 'lists' for now)
	 *
	 * @param  string - the type of a component
	 * @param  string - the type of a fetched data
	 *
	 * @return mixed  - bool false on fail, the array of objects of success
	 * @since  1.0
	 */
	public function exportFromComponent($com, $type)
	{
		if (!in_array($com, self::$importables)) {
			return false;
		}

		switch ($type) {

			case 'lists':
				return self::getComponentInstance($com)->exportLists();
			default:
				return false;
		}

		return false;
	}
}
