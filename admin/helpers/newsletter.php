<?php

/**
 * The newsltter main component helper
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Content component helper.
 *
 * @since		1.0
 */
class NewsletterHelper
{

	public static $extension = 'com_newsletter';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
				JText::_('COM_NEWSLETTER_SUBMENU_DASHBOARD'),
				'index.php?option=com_newsletter&view=dashboard',
				$vName == 'dashboard'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_NEWSLETTER_SUBMENU_NEWSLETTERS'),
				'index.php?option=com_newsletter&view=newsletters',
				$vName == 'newsletters'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_NEWSLETTER_SUBMENU_TEMPLATES'),
				'index.php?option=com_newsletter&view=templates',
				$vName == 'templates'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_NEWSLETTER_SUBMENU_SUBSCRIBERS'),
				'index.php?option=com_newsletter&view=subscribers',
				$vName == 'subscribers'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_NEWSLETTER_SUBMENU_CONFIGURATION'),
				'index.php?option=com_newsletter&view=configuration',
				$vName == 'configuration'
		);
	}

	/**
	 * Gets latest info about component from server.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function getCommonInfo()
	{
		// TODO: Move it to the ComponentHelper
		$params = JComponentHelper::getParams('com_newsletter');
		$lkey = $params->get('license_key');
		$product = $params->get('product');
		$domain = $params->get('domain');
		$monster_url = $params->get('monster_url');

		$url = $monster_url . '/license_key/' . $lkey . '/product/' . $product . '/domain/' . $domain;
		$monster = @simplexml_load_file(urlencode($url));

		if (!$monster) {
			$monster = new stdClass();
			$monster->is_valid = null;
			$monster->latest_version = null;
		}

		$file = JPATH_COMPONENT_ADMINISTRATOR . DS . 'newsletter.xml';
		$path = JPath::clean($file);
		if (file_exists($path)) {
			$info = simplexml_load_file($path);
		} else {
			$info = new JObject();
		}

		$infos = $info->attributes();
		$res = new stdClass();

		$res->is_valid = null;
		if ((string)$monster->is_valid == '1') {
			$res->is_valid = "JYES";
		}
		if ((string)$monster->is_valid == '0') {
			$res->is_valid = "JNO";
		}

		$res->latest_version  = (string)$monster->latest_version;
		$res->current_version = (string)$info->version;
		$res->copyright       = (string)$info->copyright;
		$res->license_key     = (string)$lkey;
		$res->domain          = (string)$domain;

		foreach($res as &$item) {
			if (empty($item)) {
				$item = JText::_('COM_NEWSLETTER_UNKNOWN');
			}
		}

		//var_dump($res, $monster, $info); die();

		return $res;
	}

	/**
	 * Get the extended info for a newsletter identified by id.
	 *
	 * @static
	 * @param id - the id of a letter
	 *
	 * @return array - the statistics data
	 * @since 1.0
	 */
	public static function get($id)
	{
		// Get info about newsletter plus info about using it in lists.
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('DISTINCT ns.*, (CASE WHEN l.list_id IS NULL THEN 0 ELSE 1 END) AS used_as_static')
			->from('#__newsletter_newsletters AS ns')
			->join('LEFT', '#__newsletter_lists AS l ON (ns.newsletter_id = l.send_at_reg OR ns.newsletter_id = l.send_at_unsubscribe)')
			->where('newsletter_id=' . (int)$id );

		//echo nl2br(str_replace('#__','jos_',$query)); die();
		$data = $dbo->setQuery($query)->loadAssoc();
		
		if (!empty($data)) {
			// Check if we can change the type of newsletter
			$data['type_changeable'] = (!$data['used_as_static'] && $data['sent_started'] == '0000-00-00 00:00:00');
			return $data;
		}
		return array();
	}
}