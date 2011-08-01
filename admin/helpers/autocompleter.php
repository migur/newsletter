<?php

/**
 * The autocompleter helper. Handles the JS autocompleter.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');

class AutocompleterHelper
{

	/**
	 * Create the data structure for the JS autocompleter.
	 *
	 * @return array - data for AC
	 * @since 1.0
	 */
	public static function getSubscribers()
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_subscribers');
		$query->order('name asc, email asc');

		// echo nl2br(str_replace('#__','jos_',$query));
		$data = $dbo->setQuery($query)->loadAssocList();

		$res = array();
		foreach ($data as $item) {
			$res[] = array($item['name'], $item['email'], $item['subscriber_id']);
		}

		return $res;
	}

}
