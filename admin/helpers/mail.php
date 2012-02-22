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
	const APPLICATION_HEADER = 'X-Application: Migur Newsletter';
	
	const EMAIL_NAME_HEADER = 'X-Email-Name';
	
	const NEWSLETTER_ID_HEADER = 'X-Newsletter-Id';
	
	const SUBSCRIBER_ID_HEADER = 'X-Subscriber-Id';
	/*
	 * The allowed types of a letter
	 */
	public static $types = array('plain', 'html');

	public static $bounceds = array();

	
	/**
	 * Load letter from DB, load SMTP settings
	 * TODO: Move this to NewsletterModel or NewsletterModelEntity
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

		// If letter absent then fail.
		if (!$letter) {
			return false;
		}
		
		$letter = (object) $letter->getProperties();
		$letter->params = (array) json_decode($letter->params);
		if (empty($letter->params['encoding'])) {
			$letter->params['encoding'] = 'utf-8';
		}
		PlaceholderHelper::setPlaceholders($letter->params);

		$profileEntity = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
		$profileEntity->load((int)$letter->smtp_profile_id);
		
		$letter->smtp_profile = $profileEntity->toObject();


		// Set data when using J! SMTP profile
		if ($letter->smtp_profile_id == NewsletterModelEntitySmtpprofile::JOOMLA_SMTP_ID) {

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
		JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
		JLoader::import('tables.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

		$config = new JConfig();
		$data = JArrayHelper::fromObject($config);

		$res = new JObject();
		$res->smtp_profile_id = 0;
		$res->smtp_profile_name = JText::_('COM_NEWSLETTER_JOOMLA_SMTP_PROFILE');
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
		$res->mailbox_profile_id = NewsletterTableMailboxprofile::MAILBOX_DEFAULT;
		$res->params = new stdClass();
		
		return $res;
	}

	
	/**
	 *
	 * Get SMTP default profile or J! profile if the default is not configured.
	 *
	 * @param string $name - id of a letter
	 *
	 * @return object - list of subscribers
	 * @since 1.0
	 */
	public function getDefaultSmtp($onlyId = false)
	{
		$options = JComponentHelper::getComponent('com_newsletter');
		$options = $options->params->toArray();
		
		$id = empty($options['general_smtp_default'])? 0 : (int)$options['general_smtp_default'];

		// If we need only smtpID
		if (!empty($onlyId)) {
			return $id;
		}

		// If we need full profile and it is not configured
		// or J! profile selected as the default profile
		if (empty($id)) {
			return self::getJoomlaProfile();
		}
		
		// Get profile. Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_smtp_profiles AS sp');
		$query->where('smtp_profile_id = '.(int) $id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 *
	 * Get default Mailbox profile or empty if the default is not configured.
	 *
	 * @param string $name - id of a letter
	 *
	 * @return object - list of subscribers
	 * @since 1.0
	 */
	public function getDefaultMailbox($onlyId = false)
	{
		
		$options = JComponentHelper::getComponent('com_newsletter');
		$options = $options->params->toArray();
		
		$id = empty($options['general_mailbox_default'])? 0 : (int)$options['general_mailbox_default'];

		// If we need only smtpID
		if (!empty($onlyId)) {
			return $id;
		}

		// Get profile. Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_mailbox_profiles AS sp');
		$query->where('mailbox_profile_id = '.(int) $id);
		$db->setQuery($query);
		return $db->loadObject();
	}
}
