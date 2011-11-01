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

define('_PATH_BMH', JPATH_LIBRARIES.DS.'migur'.DS.'library'.DS.'mailer'.DS.'phpmailer'.DS);
include_once(_PATH_BMH . 'class.phpmailer-bmh.php');


class MailHelper
{
	/*
	 * The allowed types of a letter
	 */
	public static $types = array('plain', 'html');

	public static $bounceds = array();

	public static $resourceLink = null;
	
	public static $mailHandler = null;
	
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


	/**
	 * Connect to POP3/IMAP Mailbox profile using standard IMAP library
	 * 
	 * @param array $mailbox Sarver parameters and user's credentials
	 * @return boolean 
	 */
	public function connect($mailbox) 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (empty(self::$mailHandler)) {
			self::$mailHandler = new BounceMailHandler();
		}
		
		$bmh = self::$mailHandler;
		
		// testing examples
		$bmh->action_function    = 'callbackAction'; // default is 'callbackAction'
		$bmh->verbose            = VERBOSE_QUIET; //VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
		//$bmh->use_fetchstructure = true; // true is default, no need to speficy
		//$bmh->testmode           = true; // false is default, no need to specify
		//$bmh->debug_body_rule    = true; // false is default, no need to specify
		//$bmh->debug_dsn_rule     = true; // false is default, no need to specify
		//$bmh->purge_unprocessed  = false; // false is default, no need to specify

		/*
		 * for local mailbox (to process .EML files)
		 */
		//$bmh->openLocalDirectory('/home/email/temp/mailbox');
		//$bmh->processMailbox();

		/*
		 * for remote mailbox
		 */
		$bmh->mailhost           = $mailbox['mailbox_server']; // your mail server
		$bmh->mailbox_username   = $mailbox['username']; // your mailbox username
		$bmh->mailbox_password   = $mailbox['password']; // your mailbox password
		$bmh->port               = $mailbox['mailbox_port']; // the port to access your mailbox, default is 143
		$bmh->service            = ($mailbox['mailbox_server_type'] == '1')? 'imap' : 'pop3'; // the service to use (imap or pop3), default is 'imap'
		$bmh->service_option     = ($mailbox['is_ssl']=='1')? 'ssl' : 'none'; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
		$bmh->boxname            = 'INBOX'; // the mailbox to access, default is 'INBOX'
		//$bmh->moveHard           = true; // default is false
		//$bmh->hardMailbox        = 'INBOX.hardtest'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
		//$bmh->moveSoft           = true; // default is false
		//$bmh->softMailbox        = 'INBOX.softtest'; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
		//$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'
		
		
		// Workaround for 
		// "Notice: Unknown: Can't connect to xxx,xxx: Connection timed out (errflg=2) in Unknown on line 0"
				// Workaround for understand notice if connection was failed
		// Unknown: in line 0. May be IMAP bug.
		$er = ini_set('error_reporting', NULL);
		$de = ini_set('dispaly_errors', '0');
		
		@$bmh->openMailbox();
		// Restore
		ini_set('error_reporting', $er);
		ini_set('dispaly_errors', $de);
		
		if (empty($bmh->_mailbox_link)) {
			return false;
		}
		
		self::$resourceLink = $bmh->_mailbox_link;
		
		return true;
	}
	
	/**
	 * Scan all mails and detect bounced ones.
	 * 
	 * @return array of bounced letters
	 */
	public function getBouncedList() 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (!empty(self::$bounceds)) {
			return self::$bounceds;
		}

		if (empty(self::$mailHandler)) {
			return false;
		}
		
		$bmh = self::$mailHandler;
		
		// Just retrieve the mails
		$bmh->disable_delete     = true; // false is default, no need to specify
		$bmh->processMailbox();
		
		return self::$bounceds;
	}

	/**
	 * Deletes the letter from inbox
	 * 
	 * @param integet/string $mid The number of letter in mailbox 
	 * 
	 * @return boolean
	 */
	public function deleteMail($mid) 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (!empty(self::$resourceLink)) {
			return @imap_delete(self::$resourceLink, $mid);
		}	
		return false;
	}

	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function closeMailbox() 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (!empty(self::$resourceLink)) {
			
			// Supress all errors and messages
			$res = imap_close(self::$resourceLink);
		}	
		
		imap_errors(); 
		imap_alerts();

		return $res;
	}
	
	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function getMailboxError() 
	{
		$error = imap_last_error(); 
		imap_errors(); 
		imap_alerts();
		
		return $error;
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
	public function getDefaultSMtp($onlyId = false)
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
		$query->where('where smtp_profile_id = '.(int) $id);
		$db->setQuery($query);
		return $this->db->loadObject();
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
		$query->where('where mailbox_profile_id = '.(int) $id);
		$db->setQuery($query);
		return $this->db->loadObject();
	}
}

/* This is a sample callback function for PHPMailer-BMH (Bounce Mail Handler).
 * This callback function will echo the results of the BMH processing.
 */

/* Callback (action) function
 * @param int     $msgnum        the message number returned by Bounce Mail Handler
 * @param string  $bounce_type   the bounce type: 'antispam','autoreply','concurrent','content_reject','command_reject','internal_error','defer','delayed'        => array('remove'=>0,'bounce_type'=>'temporary'),'dns_loop','dns_unknown','full','inactive','latin_only','other','oversize','outofoffice','unknown','unrecognized','user_reject','warning'
 * @param string  $email         the target email address
 * @param string  $subject       the subject, ignore now
 * @param string  $xheader       the XBounceHeader from the mail
 * @param boolean $remove        remove status, 1 means removed, 0 means not removed
 * @param string  $rule_no       Bounce Mail Handler detect rule no.
 * @param string  $rule_cat      Bounce Mail Handler detect rule category.
 * @param int     $totalFetched  total number of messages in the mailbox
 * @return boolean
 */
function callbackAction($msgnum, $body, $bounce_type, $email, $subject, $xheader, $remove, $rule_no=false, $rule_cat=false, $totalFetched=0) {

//	if ($subject == 'Undelivered Mail Returned to Sender') {
//		var_dump($bounce_type, $subject, $body);
//	}	
	
	// Check if this is a bounced mail
	if (empty($bounce_type)) {
		return;
	}
	
	$nid = null;
	$sid = null;
	$lid = null;

	// Lets get the IDs from headers placed in body of bounced letter
	if (preg_match_all('/subscriber-id:\s*([0-9]+)/is', $body, $matches)) {
		if (!empty($matches[1][0])) {
			$sid = $matches[1][0];
		}	
	}
	
	if (preg_match_all('/newsletter-id:\s*([0-9]+)/is', $body, $matches)) {
		if (!empty($matches[1][0])) {
			$nid = $matches[1][0];
		}	
	}
	
	if (preg_match_all('/list-id:\s*([0-9]+)/is', $body, $matches)) {
		if (!empty($matches[1][0])) {
			$lid = $matches[1][0];
		}	
	}
	
  MailHelper::$bounceds[] = (object)array(
	  'msgnum' => $msgnum, 
	  'bounce_type' => $bounce_type,
	  'subscriber_id' => $sid,
	  'newsletter_id' => $nid,
	  'list_id' => $lid,
	  'email' => $email, 
	  'subject' => $subject, 
	  'xheader' => $xheader, 
	  'remove' => $remove, 
	  'rule_no' => $rule_no, 
	  'rule_cat' => $rule_cat,
	  'totalFetched' => $totalFetched
  );
  return true;
}
