<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class acyMailingManager extends commonManager
{

	public $name = 'acyMailing';

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
		$query->select('s.email, s.name, COALESCE(l.name, "") AS list_name, FROM_UNIXTIME(s.created) AS created');
		$query->from('#__acymailing_subscriber AS s');
		$query->join('left', '#__acymailing_listsub AS sl ON sl.subid=s.subid');
		$query->join('left', '#__acymailing_list AS l ON sl.listid=l.listid');
		$query->order('s.email, list_name');
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

		// Check the subscribers table
		$res = $this->validateTable(
				'#__acymailing_subscriber',
				array('email', 'name', 'created')
		);
		if (!$res)
			return false;

		// Check the lists table
		return $this->validateTable(
			'#__acymailing_list',
			array('name')
		);
	}

}
