<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class rsMailManager extends commonManager
{

	public $name = 'RSMail!';

	/**
	 * Fetch the lists from RSMail! component to array
	 *
	 * @return array - array of objects
	 * @since  1.0
	 */
	public function exportLists()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.SubscriberEmail AS email, COALESCE(sd.FieldValue, "") AS name, "" AS list_name, FROM_UNIXTIME(s.DateSubscribed) AS created');
		$query->from('#__rsmail_subscribers AS s');
		$query->join('left', '#__rsmail_subscriber_details AS sd ON sd.IdSubscriber=s.IdSubscriber AND sd.FieldName = "name"');
		$query->order('s.SubscriberEmail, list_name');
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
	function isValid()
	{

		// Check the subscribers table
		$res = $this->validateTable(
				'#__rsmail_subscribers',
				array('IdSubscriber', 'SubscriberEmail')
		);
		if (!$res)
			return false;

		// Check the lists table
		return $this->validateTable(
			'#__rsmail_subscriber_details',
			array('IdSubscriber', 'FieldName', 'FieldValue')
		);
	}

}
