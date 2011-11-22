<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

abstract class modNewsletterSubscribeHelper
{

	public static function addHeadData()
	{
		// Get documnet
		$doc = & JFactory::getDocument();
		// Add stylesheet
		$doc->addStyleSheet("modules/mod_newsletter_subscribe/css/styles.css");
		// Add script
		$doc->addScript('modules/mod_newsletter_subscribe/scripts/subscribe.js');
	}

	public static function getSendToURL(&$params)
	{
		// Fetch application
		$application = JFactory::getApplication();
		$config = JFactory::getConfig();
		// Get menu
		$menu = $application->getMenu();
		// Get the menu url
		$item = $menu->getItem($params->get('sendto'));

		if (!is_object($item)) {
			return JRoute::_("index.php");
		} else {
			// Use SEF link, if sef
			if ($config->getValue('config.sef')) {
				return JRoute::_(JURI::base() . (!$config->getValue('config.sef_rewrite') ? "index.php/" : "") . $item->route);
			} else {
				return JRoute::_(JURI::base() . $item->link);
			}
		}
	}

	public static function getEmail()
	{
		$user = JFactory::getUser();

		if ($user->id > 0) {
			return $user->email;
		}

		return '';
	}

	public static function getName()
	{
		$user = JFactory::getUser();

		if ($user->id > 0) {
			$db = JFactory::getDbo();
			$sql = 'SELECT * FROM #__newsletter_subscribers WHERE user_id=' . $db->quote($user->id);
			$subscriber = $db->setQuery($sql)->loadObject();
			
			if (!empty($subscriber)) {
				return $subscriber->name;
			}	
		}

		return '';
	}

	public static function getList(&$params)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$listSelector = $params->get('listselector', 0);
		$multiList = $params->get('multilist', array(0));

		$query = 'SELECT list_id AS value, name AS text FROM #__newsletter_lists WHERE state=1';

		switch ($listSelector) {
			case '0':
				break;
			case '1':
				$query .= ' AND internal=0';
				break;
			case '2':
				$query .= ' AND internal=1';
				break;
			case '3':
				$query .= ' AND list_id in(' . implode(',', $multiList) . ')';
				break;
		}
		$items = $db->setQuery($query)->loadObjectList();

		return $items;
	}

	public static function getType()
	{
		return array(
			(object) array(
				'text' => JText::_('MOD_NEWSLETTER_TEXT'),
				'value' => 0
			),
			(object) array(
				'text' => JText::_('MOD_NEWSLETTER_HTML'),
				'value' => 1
			)
		);
	}

	public function getFbMe($app_id, $app_secret)
	{
		$args = array();
		
		parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
		
		ksort($args);
		$payload = '';
		foreach ($args as $key => $value) {
			if ($key != 'sig') {
				$payload .= $key . '=' . $value;
			}
		}
		if (md5($payload . $app_secret) != $args['sig']) {
			return null;
		}

		return (object)json_decode(
			@file_get_contents('https://graph.facebook.com/me?access_token='.$args['access_token'])
		);
	}

}
