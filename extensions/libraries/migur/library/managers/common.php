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
	 * Imports the lists into acyMailer component
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function importLists()
	{
		return true;
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
		$db->setQuery('DESCRIBE ' . $name);
		$result = $db->query();

		$res = array();
		if (empty($result)) {
			return false;
		}

		$fields = array();
		while ($row = mysql_fetch_array($result)) {
			$fields[] = $row[0];
		}

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
