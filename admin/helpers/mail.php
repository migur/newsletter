<?php

/**
 * The mail helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');

class MailHelper
{
	/*
	 * The allowed types of a letter
	 */
	public static $types = array('plain', 'html');


	/**
	 * Load letter from DB, load SMTP settings
	 *
	 * @param <string> $name - id of a letter
	 *
	 * @return object - letter
	 * @since 1.0
	 */
	public static function loadLetter($id = false)
	{
		$letter = JTable::getInstance('Newsletter', 'NewsletterTable');
		$letter->load((int) $id);

		if (!$letter) {
			return false;
		}
		$letter = (object) $letter->getProperties();

		if ($letter->smtp_profile_id > 0) {
			$profile = JTable::getInstance('Smtpprofile', 'NewsletterTable');
			$profile->load((int) $letter->smtp_profile_id);
		} else {
			$profile = MailHelper::getJoomlaProfile();
		}

		$letter->smtp_profile = (object) $profile->getProperties();

		$letter->params = (array) json_decode($letter->params);
		PlaceholderHelper::setPlaceholders($letter->params);
		if ($letter->smtp_profile_id < 1) {

			if (!empty($letter->params['newsletter_from_email'])) {
				$letter->smtp_profile->from_email = $letter->params['newsletter_from_email'];
			}
			if (!empty($letter->params['newsletter_from_name'])) {
				$letter->smtp_profile->from_name = $letter->params['newsletter_from_name'];
			}
			if (!empty($letter->params['newsletter_to_email'])) {
				$letter->smtp_profile->reply_to_email = $letter->params['newsletter_to_email'];
			}
			if (!empty($letter->params['newsletter_to_name'])) {
				$letter->smtp_profile->reply_to_name = $letter->params['newsletter_to_name'];
			}
		}
		return $letter;
	}

	/**
	 *
	 * Get all subscribers binded to list with $id.
	 *
	 * @param string $name - id of a letter
	 *
	 * @return object - list of subscribers
	 * @since 1.0
	 */
	protected function getSubscribersFromList($id)
	{
		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_sub_list AS a');
		$query->join('LEFT', '#__newsletter_subscribers AS s ON a.subscriber_id=s.subscriber_id');
		$query->where('where sub_list_id = "' . (int) $id . '"');
		$query->order('s.email asc');
		$db->setQuery($query);
		return $this->db->loadObjectList();
	}

	/**
	 *
	 * Filter the type of newsletter
	 *
	 * @param string $type
	 * @param string $default
	 *
	 * @return mixed - filtered type
	 * @since 1.0
	 */
	public static function filterType($type, $default = false)
	{
		$type = strtolower($type);
		if (in_array($type, self::$types)) {
			return $type;
		};

		return $default;
	}

	/**
	 * Create the "_smtp"-like profile from J! mail settings
	 *
	 * @return JObject
	 */
	public function getJoomlaProfile()
	{

		$config = new JConfig();
		$data = JArrayHelper::fromObject($config);

		$res = new JObject();
		$res->smtp_profile_id = 0;
		$res->smtp_profile_name = JText::_('COM_NEWSLETTER_JOOMLA_MAIL_SETTINGS');
		$res->from_name = $data['fromname'];
		$res->from_email = $data['mailfrom'];
		$res->reply_to_email = $data['mailfrom'];
		$res->reply_to_name = $data['fromname'];
		$res->smtp_server = $data['smtphost'];
		$res->smtp_port = $data['smtpport'];
		$res->is_ssl = (strtolower($data['smtpsecure']) == 'ssl') ? 1 : 0;
		$res->pop_before_smtp = 0;
		$res->username = $data['smtpuser'];
		$res->password = $data['smtppass'];

		return $res;
	}

}
