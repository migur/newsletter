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

JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');


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
				JText::_('COM_NEWSLETTER_SUBMENU_AUTOMAILING'),
				'index.php?option=com_newsletter&view=automailings',
				$vName == 'automailing'
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

		$domain = $_SERVER['SERVER_NAME'];
		
		$monster_url = $params->get('monster_url');

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
		$query->select("DISTINCT ns.*, (CASE WHEN l.list_id IS NULL THEN '0' ELSE '1' END) AS used_as_static")
			->from('#__newsletter_newsletters AS ns')
			->join('LEFT', '#__newsletter_lists AS l ON (ns.newsletter_id = l.send_at_reg OR ns.newsletter_id = l.send_at_unsubscribe)')
			->where('newsletter_id=' . (int) $id);

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$data = $dbo->setQuery($query)->loadAssoc();

		if (!empty($data)) {
			// Check if we can change the type of newsletter
			$data['type_changeable'] = ($data['used_as_static'] == 0 && $data['sent_started'] == '0000-00-00 00:00:00');
			$data['saveable'] = ($data['type'] == 1 || $data['sent_started'] == '0000-00-00 00:00:00');
			return $data;
		}
		return array();
	}

	/**
	 * Get first unused alias.
	 *
	 * @param  string $alias assumed alias
	 * @return string alias should been used
	 */
	public function getFreeAlias($alias)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_newsletters AS a');
		$query->where("alias LIKE '" . addslashes($alias) . "%'");

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','jos_',$query)); die;
 		$res = $db->loadAssocList();

		if (empty($res)) {
			return $alias;
		}
		
		// Get array with similar aliases
		$aliases = array();
		foreach($res as $item) {
			$aliases[] = $item['alias'];
		}

		// Find unused alias...
		for ($i = 1; $i < 100000; $i++) {
			if(!in_array($alias.$i, $aliases)) {
				return $alias.$i;
			}
		}
	}
	
	/**
	 * Get first unused alias.
	 *
	 * @param  string $alias assumed alias
	 * @return string alias should been used
	 */
	public function getByAlias($alias)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_newsletters AS a');
		$query->where("alias='" . addslashes($alias) . "'");
		$db->setQuery($query);
		//echo nl2br(str_replace('#__','jos_',$query)); die;
 		return $db->loadAssoc();
	}
	
	public function getMailProfiles($nid)
	{
		$db = JFactory::getDbo();
		
		// Get default SMTP and Mailbox profile ids
		$smtpId = MailHelper::getDefaultSmtp('idOnly');
		$mailboxId = MailHelper::getDefaultMailbox('idOnly');
		
		$newsletter = JTable::getInstance('Newsletter', 'NewsletterTable');
		$newsletter->load($nid);
		
		// The default profile is the SMTP profile
		if ($newsletter->smtp_profile_id == 0) {
			
			$smtp = (array)MailHelper::getJoomlaProfile();
			$mailbox = (array)MailHelper::getDefaultMailbox();
			
			$smtp['mailbox_profile_id'] = !empty($mailbox['mailbox_profile_id'])?
				$mailbox['mailbox_profile_id'] : 0;
			
			$res = array(
				'mailbox' => $mailbox,
				'smtp'    => $smtp
			);
			
		} else {
		
			$db->setQuery(
				'SELECT DISTINCT '.
					'mp.mailbox_profile_id AS mp_mailbox_profile_id, '.
					'mp.mailbox_profile_name AS mp_mailbox_profile_name, '.
					'mp.mailbox_server AS mp_mailbox_server, '.
					'mp.mailbox_server_type AS mp_mailbox_server_type, '.
					'mp.mailbox_port AS mp_mailbox_port, '.
					'mp.is_ssl AS mp_is_ssl, '.
					'mp.username AS mp_username, '.
					'mp.password AS mp_password, '.

					'sp.smtp_profile_id AS sp_smtp_profile_id, '.
					'sp.smtp_profile_name AS sp_smtp_profile_name, '.
					'sp.from_name AS sp_from_name, '.
					'sp.from_email AS sp_from_email, '.
					'sp.reply_to_name AS sp_reply_to_name, '.
					'sp.reply_to_email AS sp_reply_to_email, '.
					'sp.smtp_server AS sp_smtp_server, '.
					'sp.smtp_port AS sp_smtp_port, '.
					'sp.is_ssl AS sp_is_ssl, '.
					'sp.pop_before_smtp AS sp_pop_before_smtp, '.
					'sp.username AS sp_username, '.
					'sp.password AS sp_password, '.
					'sp.mailbox_profile_id AS sp_mailbox_profile_id '.

				'FROM #__newsletter_mailbox_profiles AS mp '.

				'JOIN #__newsletter_smtp_profiles AS sp '.
					'ON (sp.mailbox_profile_id = mp.mailbox_profile_id) '.
					'OR (sp.mailbox_profile_id = '.NewsletterTableMailboxprofile::MAILBOX_DEFAULT.' AND mp.mailbox_profile_id='.$mailboxId.') '.

				'JOIN #__newsletter_newsletters AS n  '.
					'ON (n.smtp_profile_id = sp.smtp_profile_id) '.
					'OR (n.smtp_profile_id = '.NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID.' AND sp.smtp_profile_id='.$smtpId.') '.

				// get mailboxes for sent newsletters without errors
				'WHERE n.newsletter_id = ' . (int)$nid
			);

			//echo (string)$db->getQuery();
			$result = $db->loadAssoc();

			$res = array(
				'mailbox' => array(
					'mailbox_profile_id' => $result['mp_mailbox_profile_id'],
					'mailbox_profile_name' => $result['mp_mailbox_profile_name'],
					'mailbox_server' => $result['mp_mailbox_server'],
					'mailbox_server_type' => $result['mp_mailbox_server_type'],
					'mailbox_port' => $result['mp_mailbox_port'],
					'is_ssl' => $result['mp_is_ssl'],
					'username' => $result['mp_username'],
					'password' => $result['mp_password']
				),

				'smtp' => array(
					'smtp_profile_id' => $result['sp_smtp_profile_id'],
					'smtp_profile_name' => $result['sp_smtp_profile_name'],
					'from_name' => $result['sp_from_name'],
					'from_email' => $result['sp_from_email'],
					'reply_to_name' => $result['sp_reply_to_name'],
					'reply_to_email' => $result['sp_reply_to_email'],
					'smtp_server' => $result['sp_smtp_server'],
					'smtp_port' => $result['sp_smtp_port'],
					'is_ssl' => $result['sp_is_ssl'],
					'pop_before_smtp' => $result['sp_pop_before_smtp'],
					'username' => $result['sp_username'],
					'password' => $result['sp_password'],
					'mailbox_profile_id' => $result['sp_mailbox_profile_id'],
				),
			);
		}
		return $res;
	}

	
	
	/**
	 * Log a messagge into file.
	 * 
	 * @param string Message
	 * @param string File name, usae current date otherwise
	 * @param boolean Use to force the logging
	 */ 
	static public function logMessage($msg, $filename = null, $force = false) 
	{
		$arr = explode('/', $filename);
		return LogHelper::addDebug($msg, $arr[0]);
	}

	
	
	/**
	 * Assumes that this is complete server response.
	 * 
	 * @param boolean $status The status of a responce
	 * @param string $message Text of returned messages
	 * @param type $data
	 * @param type $exit 
	 */
	static public function jsonResponse($status, $messages = array(), $data = null, $exit = true) {
		
		$serverResponse = ob_get_contents();
		ob_end_clean();
		
		if (!is_array($messages) && !is_object($messages)) {
			$messages = array($messages);
		}
		
		@header('Content-Type:application/json; charset=utf-8');
		
		echo json_encode(array(
			'state' => (bool)$status,
			'messages' => (array)$messages,
			'data' => $data,
			'serverResponse' => htmlentities($serverResponse)
		));
		
		ob_start();
		jexit();
	}

	static public function jsonError($messages = array(), $data = array(), $exit = true) {
		self::jsonResponse(false, $messages, $data, $exit);
	}	
	
	static public function jsonMessage($messages = array(), $data = array(), $exit = true) {
		self::jsonResponse(true, $messages, $data, $exit);
	}	
	
	
	
	/**
	 * Get parameter directly from the parameters of component 
	 * from extensions table
	 * 
	 * @param type $name
	 * @return type 
	 */
	static public function getParam($name) 
	{
		$table = JTable::getInstance('extension');
		if ( empty($table) || !$table->load(array('element' => 'com_newsletter'))) {
			return false;
		}
				
		$params = (object)json_decode($table->params);
		return isset($params->{$name})? $params->{$name} : null;
	}
	
	
	
	/**
	 * Sets the param of component directly into extensions table
	 * 
	 * @param type $name
	 * @param type $value
	 * @return type 
	 */
	static public function setParam($name, $value) 
	{
		$table = JTable::getInstance('extension');
		if ( empty($table) || !$table->load(array('element' => 'com_newsletter'))) {
			return false;
		}
		
		$params = (object)json_decode($table->params);
		$params->{$name} = $value;
		$table->params = json_encode($params);
		return $table->store();
	}
}