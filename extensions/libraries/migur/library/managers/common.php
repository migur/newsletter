<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
class commonManager
{

	/**
	 * Fetch the subscribers from acyMailer component to array
	 *
	 * @return Array
	 * @since  1.0
	 */
	public function exportSubscribers()
	{
		return array();
	}

	/**
	 * Fetch the lists from acyMailer component to array
	 *
	 * @return array - array of objects
	 * @since  1.0
	 */
	public function exportLists()
	{
		return array();
	}

	/**
	 * Imports the subscribers into acyMailer component
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function importSubscribers(array $list)
	{
		return true;
	}

	/**
	 * Imports the data about subscribers and lists into com_newsletter
	 *
	 * @param  array - the array of the objects(subscriber - list)
	 *
	 * @return mixed - integer/(bool)false on success/fail 
	 * @since  1.0
	 */
	public function importLists($list)
	{
		$lists = array();
		$subs = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$added = 0;
		
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
					
					$added++;
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

		return $added;
	}

	/**
	 * Check the structure of a exported/imported tables
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function isValid()
	{
		JError::throwError('isValid should be implemented (' . $this->getName() . ')');
	}

	public function validateTable($name, $needed)
	{
		$db = JFactory::getDbo();
		$fields = @$db->getTableColumns($name);

		if (empty($fields)) {
			return false;
		}

		$fields = array_keys($fields);
		
		foreach ($needed as $item) {
			// Check the needed fields
			if (!in_array($item, $fields)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the name.
	 * 
	 * @return string
	 * @since  1.0
	 */
	public function getName()
	{
		return $this->name;
	}

}
