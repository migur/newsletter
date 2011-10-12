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

	public static $_manifest = null;
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

		$obj = self::getManifest();
		$product = $obj->monsterName;

		$domain = $_SERVER['HTTP_HOST'];
		
		$monster_url = $params->get('monster_url');

		//$monster_url = 'monster.woody.php.nixsolutions.com';
		$url = $monster_url . '/service/check/license/license_key/' . urlencode($lkey) . '/product/' . urlencode($product) . '/domain/' . urlencode($domain);
		if (empty($url) || strpos($url, 'http://') === false) {
			$url = 'http://' . $url;
		}

                //$res = self::_getCommonInfo($url, $domain, $lkey);
		$cache = JFactory::getCache('com_newsletter');
		$res = $cache->call(array('NewsletterHelper', '_getCommonInfo'), $url, $domain, $lkey);
		$res->current_version = (string) $obj->version;
		$res->copyright = (string) $obj->copyright;
		return $res;
	}

	/**
 	 * Gets latest info about component from server.
	 * Used in cjaching
	 * 
	 * @param string $url
	 * @param string $domain
	 * @param string $lkey
	 * 
	 * @return stdClass
	 * @since	1.0
	 */
	public static function _getCommonInfo($url, $domain, $lkey)
	{
		$monster = @simplexml_load_file($url);

		if (!$monster) {
			$monster = new stdClass();
			$monster->is_valid = null;
			$monster->latest_version = null;
		}

		$res = new stdClass();
		
		$res->is_valid = null;
		if ((string) $monster->is_valid == '1') {
			$res->is_valid = "JYES";
		} else {
			$res->is_valid = "JNO";
		}

		$res->latest_version = (string) $monster->latest_version;
		$res->license_key = (string) $lkey;
		$res->domain = (string) $domain;

		foreach ($res as &$item) {
			if (empty($item)) {
				$item = JText::_('COM_NEWSLETTER_UNKNOWN');
			}
		}

		return $res;
	}

	static public function getManifest()
	{
		if (!self::$_manifest) {
			$file = JPATH_COMPONENT_ADMINISTRATOR . DS . 'newsletter.xml';
			$path = JPath::clean($file);
			if (file_exists($path)) {
				self::$_manifest = simplexml_load_file($path);
			} else {
				self::$_manifest = new JObject();
			}
		}

		return self::$_manifest;
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
			->where('newsletter_id=' . (int) $id);

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