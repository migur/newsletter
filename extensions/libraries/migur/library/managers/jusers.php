<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class jUsersManager extends commonManager
{

	public $name = 'Joomla users';

	/**
	 * Fetch the lists from acyMailer component to array
	 *
	 * @return array - array of objects
	 * @since  1.0
	 */
	public function exportLists()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.email, s.name, "" AS list_name, s.registerDate AS created, id as userId');
		$query->from('#__users AS s');
		$query->order('s.email');
		// Set the query
		$db->setQuery($query);
		$objs = $db->loadObjectList();

		return (array) $objs;
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

		// Check the users table
		return $this->validateTable(
				'#__users',
				array('id', 'name', 'username', 'email')
		);
	}

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

			$subscriber = JTable::getInstance('Subscriber', 'NewsletterTable');
			
			// Add new subscribers with not existing emails
			if (!empty($obj->email)) {
				
				$subscriber->load(array('email' => $obj->email));
				
				if (!empty($subscriber->subscriber_id)) {
					
					if (empty($subscriber->user_id) && !empty($obj->userId)) {
						$subscriber->save(array('user_id' => $obj->userId));
					}
					
					$subId = $subscriber->subscriber_id;
					
				} else {

					$subscriber->save(array(
						'email' => $obj->email,
						'name' => $obj->name,
						'created_on' => $obj->created,
						'user_id' => !empty($obj->userId)? $obj->userId : 0,
						'confirmed' => 1,
						'subscription_key' => 0
					));	
					$subKey = SubscriberHelper::createSubscriptionKey($subscriber->subscriber_id);
					
					$subscriber->save(array('subscription_key' => $subKey));
					
					$added++;
				}
			}
			
			unset($subscriber);
		}

		return $added;
	}
}
