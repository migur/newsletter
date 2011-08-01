<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class acajoomManager extends commonManager
{

	public $name = 'Acajoom';

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
		$query->select('s.email, s.name, COALESCE(l.list_name, "") AS list_name, s.subscribe_date AS created');
		$query->from('#__acajoom_subscribers AS s');
		$query->join('left', '#__acajoom_queue AS sl ON sl.subscriber_id=s.id');
		$query->join('left', '#__acajoom_lists AS l ON sl.list_id=l.id');
		$query->order('s.email, l.list_name');
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
				'#__acajoom_subscribers',
				array('id', 'email', 'name', 'subscribe_date')
		);
		if (!$res)
			return false;

		// Check the lists table
		return $this->validateTable(
			'#__acajoom_lists',
			array('id', 'list_name')
		);

		// Check the lists table
		return $this->validateTable(
			'#__acajoom_queue',
			array('subscriber_id', 'list_id')
		);
	}

}
