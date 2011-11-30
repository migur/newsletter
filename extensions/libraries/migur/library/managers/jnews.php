<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class jNewsManager extends commonManager
{

	public $name = 'jNews';

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
		$query->select('s.email, s.name, COALESCE(l.list_name, "") AS list_name, FROM_UNIXTIME(s.subscribe_date) AS created');
		$query->from('#__jnews_subscribers AS s');
		$query->join('left', '#__jnews_listssubscribers AS sl ON sl.subscriber_id=s.id');
		$query->join('left', '#__jnews_lists AS l ON sl.list_id=l.id');
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
				'#__jnews_subscribers',
				array('id', 'name', 'email', 'subscribe_date')
		);
		if (!$res)
			return false;

		// Check the lists table
		$res = $this->validateTable(
				'#__jnews_lists',
				array('list_name', 'id')
		);
		if (!$res)
			return false;

		// Check the lists table
		return $this->validateTable(
			'#__jnews_listssubscribers',
			array('list_id', 'subscriber_id')
		);
	}

}
