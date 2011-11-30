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
		$query->select('s.email, s.name, "" AS list_name, s.register_date AS created');
		$query->from('#__users AS s');
		$query->order('s.email');
		// Set the query
		$db->setQuery($query);
		$objs = $db->loadObjectList();

		var_dump($objs);
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

}
