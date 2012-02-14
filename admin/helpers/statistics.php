<?php

/**
 * The statistcs helper. Allow to get various statistics data.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class for statistics helper
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class StatisticsHelper
{

	/**
	 * Get statistics "Total sent and bounced".
	 * The source table is #_newsletter_sent. The data apears in this table ONLY
	 * after attempt to send the email. There we can find BOUNCED state. This method
	 * divides all sent emails by BOUNCED state.
	 *
	 * @static
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function totalSent($ids = null)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$subquery = 
			' SELECT DISTINCT newsletter_id, subscriber_id, bounced ' .
			' FROM #__newsletter_sent';
		
		$query->select('(CASE bounced WHEN "" THEN "NO" ELSE bounced END) AS bounced, count(*) as cnt')
			->from('(' . $subquery . ') AS ns');

		if (is_array($ids)) {
			$query->where('newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->group('bounced');

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$data = $dbo->setQuery($query)->loadAssocList();

		$res = array();
		$total = 0;
		foreach ($data as $item) {
			$res[strtolower($item['bounced'])] = intval($item['cnt']);
			$total += intval($item['cnt']);
		}

		$res['total'] = $total;

		$data = array(
			'no' => empty($res['no']) ? 0 : $res['no'],
			'soft' => empty($res['soft']) ? 0 : $res['soft'],
			'hard' => empty($res['hard']) ? 0 : $res['hard'],
			'technical' => empty($res['technical']) ? 0 : $res['technical'],
			'total' => empty($res['total']) ? 0 : $res['total']
		);

		return $data;
	}

	/**
	 * Get count of "open" actions. All actions or for the list of newsletters.
	 *
	 * @static
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function openedActionsCount($ids = null)
	{

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->select('DISTINCT (CASE h.action WHEN "4" THEN "OPENED" ELSE "OTHER" END) AS action, count(*) as cnt')
			->from('#__newsletter_sub_history as h')
			->where('h.action="' . NewsletterTableHistory::ACTION_OPENED . '"');			

		if (is_array($ids)) {
			$query->join('','#__newsletter_newsletters AS ns ON ns.newsletter_id = h.newsletter_id');
			$query->where('ns.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->group('h.action');

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$data = $dbo->setQuery($query)->loadAssocList();

		$res = array();
		$total = 0;
		foreach ($data as $item) {
			
			if (empty($res[strtolower($item['action'])])) {
				$res[strtolower($item['action'])] = 0;
			}
			$res[strtolower($item['action'])] += intval($item['cnt']);
			
			$total += intval($item['cnt']);
		}

		$res['total'] = $total;

		$data = array(
			'other' => empty($res['other']) ? 0 : $res['other'],
			'opened' => empty($res['opened']) ? 0 : $res['opened'],
			'total' => empty($res['total']) ? 0 : $res['total']
		);

		/* 		arrray(
		  'opened'=>
		  'other' =>
		  'total' => )
		 */

		return $data;
	}

	/**
	 * Get total count of opened newsletters.
	 * Active subscriber = Subscriber that got a newsletter and opened it.
	 *
	 * @static
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function openedNewslettersCount($ids = null)
	{
		$dbo = JFactory::getDbo();

		$res = array();

		$query = $dbo->getQuery(true);
		$query->select('distinct h.newsletter_id, h.subscriber_id')
			->from('#__newsletter_sub_history AS h');

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->where('h.action="' . NewsletterTableHistory::ACTION_OPENED . '"');

		//echo nl2br(str_replace('#__','jos_',$query));
		$res['newsletters'] = count($dbo->setQuery($query)->loadAssocList());

		$query = $dbo->getQuery(true);
		$query->select('distinct h.subscriber_id')
			->from('#__newsletter_sub_history as h');
			//->join('', '#__newsletter_subscribers AS s ON s.subscriber_id=h.subscriber_id');

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->where('h.action="' . NewsletterTableHistory::ACTION_CLICKED . '"');

		//echo nl2br(str_replace('#__','jos_',$query));
		$res['subscribers'] = count($dbo->setQuery($query)->loadAssocList());

		return $res;
	}

	/**
	 * Get new subscribers during period".
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 *
	 * @return int - the statistics data
	 * @since 1.0
	 */
	public static function newSubscribersCount($startDate, $endDate)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 00:00:00", strtotime($endDate));

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('count(*) as cnt')
			->from('#__newsletter_subscribers AS s')
			->where('created_on >= "' . $startDate . '" AND created_on <= "' . $endDate . '"');

		//echo nl2br(str_replace('#__','jos_',$query));
		$res = $dbo->setQuery($query)->loadAssoc();
		return $res['cnt'];
	}

	/**
	 * Get the added (added to any list) subscribers during period".
	 * Total subscribres = all subscribers in db with connected list
	 * Action: "Added to list" in history
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 *
	 * @return int - the count
	 * @since 1.0
	 */
	public static function totalSubscribersCount($startDate, $endDate, $ids = null)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 00:00:00", strtotime($endDate));


		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('h.subscriber_id, count(*) as cnt')
			->from('#__newsletter_sub_history as h');

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->where('h.action="' . NewsletterTableHistory::ACTION_ADDED . '"')
			->where('h.date >= "' . $startDate . '" AND h.date <= "' . $endDate . '"')
			->group('h.subscriber_id');

		//echo nl2br(str_replace('#__','jos_',$query));
		return count($dbo->setQuery($query)->loadAssocList());
	}

	/**
	 * Get the added (added to any list) subscribers during period".
	 * Lost subscribers = Subscriber that exists but are unsubscribed from all lists.
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 *
	 * @return int - the statistics data
	 * @since 1.0
	 */
	public static function lostSubscribersCount($startDate, $endDate, $ids = null)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 00:00:00", strtotime($endDate));


		$dbo = JFactory::getDbo();

		// Be based on that each UNSUBSCRIBE should have previous the ADD_LIST

		$query = $dbo->getQuery(true);
		$query->select('h.subscriber_id, count(*) AS cnt1, count(h2.subscriber_id) as cnt2')
			->from('#__newsletter_sub_history as h')
			->join('LEFT',
				'#__newsletter_sub_history as h2 ON ' .
				'h.subscriber_id=h2.subscriber_id AND ' .
				'h.list_id=h2.list_id AND ' .
				'h2.date >= "' . $startDate . '" AND h2.date <= "' . $endDate . '" AND ' .
				'h2.action="' . NewsletterTableHistory::ACTION_UNSUBSCRIBED . '" AND ' .
				'h.date < h2.date'
			)
			->where('h.action="' . NewsletterTableHistory::ACTION_ADDED . '"')
			->where('h.date < "' . $endDate . '"');

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		$query->group('h.subscriber_id HAVING cnt1=cnt2');

		//echo nl2br(str_replace('#__','jos_',$query));
		return count($dbo->setQuery($query)->loadAssocList());
	}

	/**
	 * Get count of active subscribers for a period selected period and
	 * selected list of newsletters.
	 * Active subscriber = Subscriber that got a newsletter and opened it.
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function activeSubscribersCount($startDate, $endDate, $ids = null)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 23:59:59", strtotime($endDate));

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('DISTINCT subscriber_id')
			->from('#__newsletter_sub_history as h')
			->where('h.action="' . NewsletterTableHistory::ACTION_OPENED . '"')
			->where('h.date >= "' . $startDate . '" AND h.date <= "' . $endDate . '"');

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		return count($dbo->setQuery($query)->loadAssocList());
	}

	/**
	 * Get total clicks for newsletters.
	 *
	 * @static
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function totalClicks($ids = null)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('count(*) as total')
			->from('#__newsletter_sub_history as h')
			->where('h.action="' . NewsletterTableHistory::ACTION_CLICKED . '"');
		
		if (!empty($ids)) {
			$query->where('h.newsletter_id in (' . implode(',', $ids) . ')');
		}	

		//echo nl2br(str_replace('#__','jos_',$query));
		$res = $dbo->setQuery($query)->loadAssoc();
		return array('total' => (int) $res['total']);

		//Example:
		//return array('total' => 123);
	}

	/**
	 * Get total count of selected ACTIVITY for newsletters or
	 * activity for selected range. 
	 * No matter if the letter exists or not. Main goal is activity = data from history
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 * @param activity string - the type of activity
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function activityPerDay($startDate, $endDate, $ids, $activity)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 23:59:59", strtotime($endDate));

		//TODO: Implement the functionality for calculating statistics
		$dbo = JFactory::getDbo();

		// Be based on that each UNSUBSCRIBE should have previous the ADD_LIST

		$query = $dbo->getQuery(true);
		$query->select('DATE(date) AS day, count(*) as count')
			->from('#__newsletter_sub_history as h')
		//	->join('', '#__newsletter_newsletters as n ON n.newsletter_id=h.newsletter_id')
			->where('h.action=' . $dbo->quote($activity))
			->group('day');

		if (!empty($startDate)) {
			$query->where('h.date >= "' . $startDate . '"');
		}

		if (!empty($endDate)) {
			$query->where('h.date <= "' . $endDate . '"');
		}

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}


		//echo nl2br(str_replace('#__', 'jos_', $query));
		$res = $dbo->setQuery($query)->loadAssocList();


		// add absent dates
		$assoc = array();
		foreach ($res as $item) {
			$assoc[$item['day']] = (int) $item['count'];
		}
		unset($res);

		$date = date('Y-m-d', strtotime($startDate));
		$end = date('Y-m-d', strtotime($endDate));
		
		while ($date <= $endDate) {

			if (!isset($assoc[$date])) {
				$assoc[$date] = 0;
			}

			$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
		}

		ksort($assoc);
		return $assoc;
	}

	/**
	 * Get total count of active subscribers per day for newsletters and
	 * selected range.
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function activeSubscribersPerDay($startDate, $endDate, $ids)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 23:59:59", strtotime($endDate));

		//TODO: Implement the functionality for calculating statistics
		$dbo = JFactory::getDbo();

		// Be based on that each UNSUBSCRIBE should have previous the ADD_LIST

		$query = $dbo->getQuery(true);
		$query->select('DATE(date) AS day, count(*) as count')
			->from('#__newsletter_sub_history as h')
			->join('', '#__newsletter_newsletters as n ON n.newsletter_id=h.newsletter_id')
			->where('h.action="' . NewsletterTableHistory::ACTION_OPENED . '"')
			->group('day, h.subscriber_id');

		if (!empty($startDate)) {
			$query->where('h.date >= "' . $startDate . '"');
		}

		if (!empty($endDate)) {
			$query->where('h.date <= "' . $endDate . '"');
		}

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}


		//echo nl2br(str_replace('#__', 'jos_', $query));die;
		$res = $dbo->setQuery($query)->loadAssocList();


		// add absent dates
		$assoc = array();
		foreach ($res as $item) {
			if (empty($assoc[$item['day']])) {
				$assoc[$item['day']] = 0;
			}
			$assoc[$item['day']]++;
		}
		unset($res);

		$date = date('Y-m-d', strtotime($startDate));
		$end = date('Y-m-d', strtotime($endDate));
		$theDay = 3600 * 24;
		while ($date <= $endDate) {

			if (!isset($assoc[$date])) {
				$assoc[$date] = 0;
			}

			$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
		}

		ksort($assoc);
		return $assoc;
	}

	
	/**
	 * Get total count of oppened newsletters per day for newsletters and
	 * selected range.
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function openedNewslettersPerDay($startDate, $endDate, $ids)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 23:59:59", strtotime($endDate));

		$dbo = JFactory::getDbo();

		// Query to get rows "date - newsletter_id - subscriber_id"
		// Couple of "newsletter_id - subscriber_id" is ONE REAL mail (newsletter for subscriber)
		$query = $dbo->getQuery(true);
		$query->select('DISTINCT DATE(date) AS day, newsletter_id, subscriber_id')
			->from('#__newsletter_sub_history as h')
			->where('h.action="' . NewsletterTableHistory::ACTION_OPENED . '"');

		if (!empty($startDate)) {
			$query->where('h.date >= "' . $startDate . '"');
		}

		if (!empty($endDate)) {
			$query->where('h.date <= "' . $endDate . '"');
		}

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}

		// Query to calculate count of rows "date - newsletter_id - subscriber_id" 
		// for each day
		$query = 'SELECT day, COUNT(*) as cnt FROM ('.(string)$query.') as h GROUP BY day';

		//echo nl2br(str_replace('#__', 'jos_', $query));die;
		$res = $dbo->setQuery($query)->loadAssocList();


		// add absent dates
		$assoc = array();
		foreach ($res as $item) {
			$assoc[$item['day']] = $item['cnt'];
		}
		unset($res);

		$date = date('Y-m-d', strtotime($startDate));
		$end = date('Y-m-d', strtotime($endDate));
		$theDay = 3600 * 24;
		while ($date <= $endDate) {

			if (!isset($assoc[$date])) {
				$assoc[$date] = 0;
			}

			$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
		}

		ksort($assoc);
		return $assoc;
	}
	
	/**
	 * Get amount of new subscribers for each day during period".
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 *
	 * @return int - the statistics data
	 * @since 1.0
	 */
	public static function newSubscribersPerDay($startDate, $endDate)
	{
		$startDate = date("Y-m-d 00:00:00", strtotime($startDate));
		$endDate = date("Y-m-d 00:00:00", strtotime($endDate));

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('DATE(created_on) AS day, count(*) as count')
			->from('#__newsletter_subscribers AS s')
			->where('created_on >= "' . $startDate . '" AND created_on <= "' . $endDate . '"')
			->group('day, s.subscriber_id');

		//echo nl2br(str_replace('#__', 'jos_', $query));die;
		$res = $dbo->setQuery($query)->loadAssocList();

		self::_fillDate($startDate, $endDate, $res);
		
		ksort($res);
		return $res;
	}

	/**
	 * Get total activity for newsletters per hours.
	 *
	 * @static
	 * @param start date ('YYYY-MM-DD HH:MM:SS')
	 * @param end   date ('YYYY-MM-DD HH:MM:SS')
	 * @param ids   array of newsletter_id
	 * @param activity string - the type of activity
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function activityPerHour($startDate, $endDate, $ids, $activity)
	{
		$startDate = date("Y-m-d H:00:00", strtotime($startDate));
		$endDate = date("Y-m-d H:59:59", strtotime($endDate));

		//TODO: Implement the functionality for calculating statistics
		$dbo = JFactory::getDbo();

		// Be based on that each UNSUBSCRIBE should have previous the ADD_LIST

		$query = $dbo->getQuery(true);
		$query->select('DATE_FORMAT(h.date, "%Y-%m-%d %H") AS day, count(*) as count')
			->from('#__newsletter_sub_history as h')
			->join('', '#__newsletter_newsletters as n ON n.newsletter_id=h.newsletter_id')
			->where('h.action=' . $dbo->quote($activity))
			->group('day');

		if (!empty($startDate)) {
			$query->where('h.date >= "' . $startDate . '"');
		}

		if (!empty($endDate)) {
			$query->where('h.date <= "' . $endDate . '"');
		}

		if (is_array($ids)) {
			$query->where('h.newsletter_id in(' . implode(',', $ids) . ') ');
		}


		//echo nl2br(str_replace('#__', 'jos_', $query));die;
		$res = $dbo->setQuery($query)->loadAssocList();


		// add absent dates
		$assoc = array();
		foreach ($res as $item) {
			$assoc[$item['day']] = (int) $item['count'];
		}
		unset($res);

		$date = strtotime($startDate);
		$end = strtotime($endDate);
		$theHour = 3600;
		while ($date <= $end) {

			$dateStr = date('Y-m-d H', $date);
			if (!isset($assoc[$dateStr])) {
				$assoc[$dateStr] = 0;
			}

			$date += $theHour;
		}

		ksort($assoc);
		
		return $assoc;
	}

	protected function _fillDate($startDate, $endDate, &$res)
	{
		// add absent dates
		$assoc = array();
		foreach ($res as $item) {
			$assoc[$item['day']] = (int) $item['count'];
		}

		$date = date('Y-m-d', strtotime($startDate));
		$end = date('Y-m-d', strtotime($endDate));
		
		while ($date <= $endDate) {

			if (!isset($assoc[$date])) {
				$assoc[$date] = 0;
			}

			$date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
		}
		$res = $assoc;
	}
}
