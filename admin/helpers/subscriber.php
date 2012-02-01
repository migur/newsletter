<?php

/**
 * The subscriber helper. Allow to manipulate the subscribers data.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class for subscriber helper
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class SubscriberHelper
{
	/* Containers for data of real(logined now) user */

	public static $_user;
	public static $_registry;

	/**
	 * Set all data of the current subscriber as he has been logined in system
	 * Don't trigger any plugins events
	 *
	 * @param int the subscriber id (not j! user id)
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public static function emulateUser($params)
	{

		if (empty($params['email']) && empty($params['subscriber_id'])) {
			return false;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__newsletter_subscribers');
		if (!empty($params['email'])) {
			$query->where('email="' . $params['email'] . '"');
		}
		if (!empty($params['subscriber_id'])) {
			$query->where('subscriber_id="' . (int) $params['subscriber_id'] . '"');
		}


		$db->setQuery($query);
		$subscriber = $db->loadObject();
		$user = JUser::getInstance();

		if (!empty($subscriber->user_id)) {
			// Get a database object
			$user->load($subscriber->user_id);
		}

		// bind data
		if (!empty($subscriber->subscriber_id)) {
			$user->set('subscriber_id', $subscriber->subscriber_id);
			$user->set('name', $subscriber->name);
			$user->set('email', $subscriber->email);
			$user->set('subscription_key', $subscriber->subscription_key);
		}

		// Mark the user as logged in
		$user->set('guest', 0);
		$user->set('isRoot', true);

		// Register the needed session variables
		$session = JFactory::getSession();
		$session->set('user', $user);
		$session->set('registry', null);


		// fetch the dynamic data for placeholders
		PlaceholderHelper::setPlaceholders(array(
				'username' => $user->name,
				'useremail' => $user->email,
				'userid' => !empty($subscriber->subscriber_id) ? $user->subscriber_id : null,
				'subscription key' => !empty($user->subscription_key) ? $user->subscription_key : null
			));

		return $user;
	}

	/**
	 * Saves all the metadata about current (logined) user.
	 * It is needed to restore it after emulation of session for subscribers
	 * via emulateUser method.
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function saveRealUser()
	{
		$session = JFactory::getSession();
		self::$_user = $session->get('user');
		self::$_registry = $session->get('registry');
	}

	/**
	 * Restore the metada of current user after emulation
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function restoreRealUser()
	{
		$session = JFactory::getSession();
		$session->set('user', self::$_user);
		$session->set('registry', self::$_registry);
	}

	/**
	 * Creates the subscription key. Use user id and random number
	 * Length of subscription key is 15 characters
	 *
	 * @param  $userId - integer. The user ID.
	 *
	 * @return string
	 * @since 1.0
	 */
	public static function createSubscriptionKey($userId)
	{
		if (empty($userId)) {
			return false;
		}

		// to get the constant length
		$mask = '000000000';
		$id = substr($mask, 0, strlen($mask) - strlen($userId)) . $userId;
		$key = rand(100000, 999999) . $id . time();
		return $key;
	}

	public static function setSubscriptionKey($userId) {

		$subKey = self::createSubscriptionKey($userId);

		$db = JFactory::getDbo();
		$query = $db->setQuery(
			'UPDATE #__newsletter_subscribers '.
			'SET subscription_key="'.$subKey.'" ' .
			'WHERE subscriber_id = "'.$userId.'" LIMIT 1');

		return $db->query();
	}

	/**
	 * Get user by email. If user is absent returns the empty object
	 *
	 * @param  $email - string. The user email.
	 *
	 * @return object
	 * @since 1.0
	 */
	public static function getByEmail($email)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__newsletter_subscribers');
		$query->where('email="' . addslashes($email) . '"');
		$db->setQuery($query);
		$subscriber = $db->loadObject();

		if (empty($subscriber)) {
			$subscriber = new StdClass();
			$subscriber->email = $email;
		}
		return $subscriber;
	}

	/**
	 * Get user by subscription key. If user is absent returns the empty object
	 * Length of subscription key should be 15 characters.
	 *
	 * @param  $subkey - string. The user subscription key.
	 *
	 * @return object
	 * @since 1.0
	 */
	public static function getBySubkey($subkey)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__newsletter_subscribers');
		$query->where('subscription_key="' . addslashes($subkey) . '"');
		$db->setQuery($query);
		$subscriber = $db->loadObject();

		if (empty($subscriber)) {
			$subscriber = new StdClass();
			$subscriber->subscription_key = $subkey;
		}
		return $subscriber;
	}



	/**
	 * Get user's lists.
	 *
	 * @param  $subkey - string. The user subscription key.
	 *
	 * @return object
	 * @since 1.0
	 */
	static public function getLists($subkey)
	{
		// Initialise variables.
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT * ' .
			'FROM #__newsletter_subscribers AS s ' .
			'JOIN #__newsletter_sub_list AS sl ON s.subscriber_id = sl.subscriber_id ' .
			'JOIN #__newsletter_lists AS l ON l.list_id = sl.list_id ' .
			'WHERE s.subscription_key = ' . $db->quote(addslashes($subkey))
		);
		$items = $db->loadObjectList();
		return $items;
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