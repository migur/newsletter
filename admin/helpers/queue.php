<?php

/**
 * The queue helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class QueueHelper
{

	/**
	 * Gets the count of active newsletters in queue.
	 *
	 * @return int - count
	 * @since 1.0
	 */
	public static function getCount()
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		// Select the required fields from the table.
		$query->select('newsletter_id, SUM(CASE WHEN state=1 THEN 1 ELSE 0 END) AS to_send, SUM(CASE WHEN state=1 THEN 0 ELSE 1 END) AS sent, COUNT(*) AS total');
		$query->from('#__newsletter_queue');
		$query->group('newsletter_id');
		
		//echo nl2br(str_replace('#__','jos_',$query->__toString())); die;
		$data = $dbo->setQuery($query)->loadAssocList();
		return $data;
	}

}
