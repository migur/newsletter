<?php

/**
 * The mail sender. Allow to setup and send the letter via SMTP.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

define('_PATH_BMH', JPATH_LIBRARIES . DS . 'migur' . DS . 'library' . DS . 'mailer' . DS . 'phpmailer' . DS);
include_once(_PATH_BMH . 'class.phpmailer-bmh.php');

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Framework
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurMailerMailbox
{

	/**
	 * Array contains the emails that have been detected as bounced
	 */
	public $bounceds = array();

	public $disableDelete = true;

	public $error = array();

	public $fetched = 0;

	public $lastPosition = 0;
	
	public $messages = array();

	public $mailboxProfile = null;

	public $moveHard = false;
	
	// Array contains the emails that have been detected as bounced
	public $protocol = array();
	
	public $state = 'notInitialized';

	public $total = 0;

	public $useCache = false;
	

	
	public function __construct(&$options)
	{
		$this->mailboxProfile = $options;
		
		if (empty($this->protocol)) {
			$this->setProtocol($options);
		}
		
		$this->state = 'disconnected';
	}

	/**
	 * Turn on/off cache using
	 * 
	 * @param boolean $flag 
	 */
	public function useCache($flag = true) {
		$this->useCache = $flag;
	}
	
	/**
	 * Is connected?
	 * @return boolean
	 */
	public function isConnected()
	{
		return ($this->state == 'connected');
	}

	/**
	 * Is disconnected?
	 * @return boolean
	 */
	public function isDisconnected()
	{
		return ($this->state == 'disconnected');
	}
	
	/**
	 * 
	 */
	protected function setProtocol($options)
	{
		if (empty($this->protocol)) {

			if (!isset($options['mailbox_server_type'])) {
				throw new Exception('Unknown type of protocol');
			}

			$options['mailbox_server_type'] =
				($options['mailbox_server_type'] == 0) ? 'pop3' : 'imap';

			$options['is_ssl'] =
				($options['is_ssl'] == '1') ? 'ssl' : 'none';

			$name = 'MigurMailerProtocol' . ucfirst($options['mailbox_server_type']);

			require_once 'protocol' . DS . strtolower($options['mailbox_server_type']) . '.php';

			$this->protocol = new $name($options);
		}

		return $this->protocol;
	}

	/**
	 * Connect to POP3/IMAP Mailbox profile using standard IMAP library
	 * 
	 * @param array $mailbox Sarver parameters and user's credentials
	 * @return boolean 
	 */
	public function connect()
	{
		if (!$this->isDisconnected()) {
			return true;
		}

		$res = $this->protocol->connect();

		$this->state = ($res) ? 'connected' : 'disconnected';

		// Workaround to be able to use phpmailer-bmh
		$this->_mailbox_link = $this->protocol->getMailResource();

		if (!$res) {
			$this->setError($this->protocol->getLastError());
		}
		
		return $res;
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
		if (!$this->isConnected()) {
			return false;
		}

		if($this->protocol->deleteMail($mid)) {
//
//			if (isset($this->mailboxProfile['data'][???])) {
//				unset($this->mailboxProfile['data'][???]);
//			}	
//			
			return true;
		}

		$this->setError($this->protocol->getLastError());
		return false;
	}

	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function close()
	{
		if (!$this->isConnected()) {
			return false;
		}

		return $this->protocol->close();
	}


	/**
	 * Scan all mails and detect bounced ones.
	 * 
	 * @return array of bounced letters
	 */
	public function getBouncedList($limit)
	{
		if (!$this->connect()) {
			return false;
		}

		$this->bounceds = array();
		
		if(!$this->processMailbox($limit)) {
			
			$this->setEror($this->protocol->getLastError());
			return false;
		}
		
		return $this->bounceds;
	}

	/**
	 * Process the messages in a mailbox
	 * @param string $max       (maximum limit messages processed in one batch, if not given uses the property $maxMessages
	 * @return boolean
	 */
	function processMailbox($max = false, $offset = false)
	{
		if (!$this->isConnected()) {
			return false;
		}
		
		$this->protocol->setTimeout(6000);
		$met = ini_get('max_execution_time');

//		if ($this->moveHard && ( $this->disableDelete === false )) {
//			$this->disableDelete = true;
//		}

		$c_deleted = 0;
		$c_moved = 0;
		$useFetchstructure = false;


		// initialize counters
		if ($offset === false) {
			$x = $this->_getStartPosition();
		} else {
			$x = $offset;
		}	
		
		$c_total = $this->protocol->getMessagesCount();
		$this->total = $c_total;

		// Gmail 4000 mails = 30sec
		// Nix 4000 mails = 180sec
		//$res = $this->protocol->sort('SORTARRIVAL');

		// Gmail 4000 mails = 30sec
		// Nix 4000 mails = 180sec
		//$res = $this->protocol->since(0);

		$res = $this->protocol->findBy('body', 'Holiday Greetings');

		NewsletterHelper::logMessage('Sort.Dump:'.json_encode($res), 'mailbox/'); die;

		// Handle partial processing option
		if (!empty($max)) {
			
			$this->maxMessages = $max;
			
			if ($x+($max-1) < $c_total) {
				$c_total = $x + ($max-1);
			}	
		}
		
		$this->lastPosition = $x;
		for (; $x <= $c_total; $x++) {

			$time = mktime();
			// Think 5 minutes to retrive 1 message is more than enough
			set_time_limit(300);
			
			$messageId = '';
			$processed = false;
			
			// fetch the messages one at a time
			if ($useFetchstructure) {
				$structure = $this->protocol->getMessageBodyStructure($x);
				if ($structure->type == 1 && $structure->ifsubtype && $structure->subtype == 'REPORT' && $structure->ifparameters && $this->isParameter($structure->parameters, 'REPORT-TYPE', 'delivery-status')) {
					$processed = $this->processBounce($x, 'DSN', $c_total);
				} else { // not standard DSN msg
					$this->output('Msg #' . $x . ' is not a standard DSN message', VERBOSE_REPORT);
					$processed = $this->processBounce($x, 'BODY', $c_total);
				}
			} else {
				
				$header = $this->protocol->getMessageHeaderStructure($x);
				//$header = $this->protocol->getMessageHeaders($x);
				
				// Get the message id
				$inCache = false;
				
				if (preg_match("/Message\-ID\:\s*(\<[^\>]+\>)/is", $header, $match)) {
					$messageId = $match[1];
					$inCache = $this->checkCache($messageId);
				}

				if (preg_match('/^Date\:\s*(.+)\s*$/im', $header, $match)) {
					$date = strtotime(trim($match[1], "\r\n"));
				}
				
				if (!$inCache) {
					
					$this->addToCache($messageId);
					
					// Could be multi-line, if the new line begins with SPACE or HTAB
					if (preg_match("/Content-Type:((?:[^\n]|\n[\t ])+)(?:\n[^\t ]|$)/is", $header, $match)) {
						if (preg_match("/multipart\/report/is", $match[1]) && preg_match("/report-type=[\"']?delivery-status[\"']?/is", $match[1])) {
							// standard DSN msg
							$processed = $this->processBounce($x, 'DSN', $c_total);
						} else { // not standard DSN msg
							$this->output('Msg #' . $x . ' is not a standard DSN message', VERBOSE_REPORT);
							$processed = $this->processBounce($x, 'BODY', $c_total);
						}
					} else { // didn't get content-type header
						$this->output('Msg #' . $x . ' is not a well-formatted MIME mail, missing Content-Type', VERBOSE_REPORT);
						$processed = $this->processBounce($x, 'BODY', $c_total);
					}
					
					NewsletterHelper::logMessage('Mailbox.Mail processed.Position:'.$x.',time:'.(string)(mktime()-$time).',id:'.$messageId.',date:'.$date, 'mailbox/');
					
					$this->fetched++;
				} else {
					
					NewsletterHelper::logMessage('Mailbox.Mail in cache.Position:'.$x.',id:'.$messageId, 'mailbox/');
				}
			}
			
			$this->lastPosition = $x;

			$this->setLastDate($date);
			
		}
		
		// Restore the time limit
		set_time_limit($met);
		
		return true;
	}

	/**
	 * Function to determine if a particular value is found in a imap_fetchstructure key
	 * @param array  $currParameters (imap_fetstructure parameters)
	 * @param string $varKey         (imap_fetstructure key)
	 * @param string $varValue       (value to check for)
	 * @return boolean
	 */
	function isParameter($currParameters, $varKey, $varValue)
	{
		foreach ($currParameters as $key => $value) {
			if ($key == $varKey) {
				if ($value == $varValue) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Function to process each individual message
	 * @param int    $pos            (message number)
	 * @param string $type           (DNS or BODY type)
	 * @param string $totalFetched   (total number of messages in mailbox)
	 * @return boolean
	 */
	function processBounceDeprecated($pos, $type, $totalFetched)
	{
		$time = mktime();
		//var_dump('process bounce ' . $pos .'-'.  $type .'-'.  $totalFetched);
		$header = $this->protocol->getMessageHeaders($pos); //imap_header
		//var_dump('headers ' . (mktime() - $time));

		if (!empty($header->subject)) {
			$subject = strip_tags($header->subject);
		} else {
			$subject = '';
		}

		$met = ini_get('max_execution_time');
		if ($met < 6000 && $met != 0) {
			set_time_limit(6000);
		}

		$this->protocol->setTimeout(6000);

		//var_dump($header, $subject);

		if ($type == 'DSN') {
			// first part of DSN (Delivery Status Notification), human-readable explanation
			//var_dump('getMessageBody ' . (mktime() - $time));
			$dsn_msg = $this->protocol->getMessageBody($pos, "1");
			//var_dump('getMessageBody(end) ' . (mktime() - $time));
			$dsn_msg_structure = $this->protocol->getMessageBodyStruct($pos, "1");
			//var_dump('getMessageBodyStruct(end) ' . (mktime() - $time));

			if ($dsn_msg_structure->encoding == 4) {
				$dsn_msg = quoted_printable_decode($dsn_msg);
			} elseif ($dsn_msg_structure->encoding == 3) {
				$dsn_msg = base64_decode($dsn_msg);
			}

			// second part of DSN (Delivery Status Notification), delivery-status
			$dsn_report = $this->protocol->getMessageBody($pos, "2");//imap_fetchbody($this->_mailbox_link, $pos, "2");
			//var_dump('getMessageBody(pos2)-end ' . (mktime() - $time));

			// process bounces by rules
			$result = bmhDSNRules($dsn_msg, $dsn_report, $this->debug_dsn_rule);
		} elseif ($type == 'BODY') {
			
			//var_dump('getMessageBodyStructure ' . (mktime() - $time));
			$structure = $this->protocol->getMessageBodyStructure($pos); // imap_fetchstructure($this->_mailbox_link, $pos);
			//var_dump('getMessageBodyStructure-end ' . (mktime() - $time));
			
			switch ($structure->type) {
				case 0: // Content-type = text
				case 1: // Content-type = multipart
					//var_dump('getMessageBody2 ' . (mktime() - $time));
					$body = $this->protocol->getMessageFetchedBody($pos, "1");//imap_fetchbody($this->_mailbox_link, $pos, "1");
					//var_dump('getMessageBody2-end ' . (mktime() - $time));
					// Detect encoding and decode - only base64
					if (!empty($structure->parts[0]->encoding) && $structure->parts[0]->encoding == 4) {
						$body = quoted_printable_decode($body);
					} elseif (!empty($structure->parts[0]->encoding) && $structure->parts[0]->encoding == 3) {
						$body = base64_decode($body);
					}
					
					//var_dump('bmhBodyRules ' . (mktime() - $time) . ' body length ' . strlen($body));
					$result = bmhBodyRules($body, $structure);
					//var_dump('bmhBodyRules-end ' . (mktime() - $time));
					break;
				case 2: // Content-type = message
					//var_dump('getMessageBody3 ' . (mktime() - $time));
					
					$body = $this->protocol->getMessageBody($pos);//imap_body($this->_mailbox_link, $pos);
					//var_dump('getMessageBody3-end ' . (mktime() - $time));
					
					if ($structure->encoding == 4) {
						$body = quoted_printable_decode($body);
					} elseif ($structure->encoding == 3) {
						$body = base64_decode($body);
					}
					$body = substr($body, 0, 1000);
					//var_dump('bmhBodyRules ' . (mktime() - $time));
					$result = bmhBodyRules($body, $structure);
					//var_dump('bmhBodyRules-end ' . (mktime() - $time));
					break;
				default: // unsupport Content-type
					$this->output('Msg #' . $pos . ' is unsupported Content-Type:' . $structure->type, VERBOSE_REPORT);
					return false;
			}
		} else { // internal error
			$this->error_msg = 'Internal Error: unknown type';
			return false;
		}
		$email = $result['email'];
		$bounce_type = $result['bounce_type'];
		if ($this->moveHard && $result['remove'] == 1) {
			$remove = 'moved (hard)';
		} 
//			elseif ($this->moveSoft && $result['remove'] == 1) {
//			$remove = 'moved (soft)';	} 
		elseif ($this->disableDelete) {
			$remove = 0;
		} else {
			$remove = $result['remove'];
		}
		$rule_no = $result['rule_no'];
		$rule_cat = $result['rule_cat'];
		$xheader = false;

		if ($rule_no == '0000') { // internal error      return false;
			// code below will use the Callback function, but return no value
			if (trim($email) == '') {
				if (!empty($header->fromaddress)) {
					$email = $header->fromaddress;
				}	
			}
			$params = array($pos, $body, $bounce_type, $email, $subject, $xheader, $remove, $rule_no, $rule_cat, $totalFetched);
			call_user_func_array(array($this, 'callbackAction'), $params);
		} else { // match rule, do bounce action
			$params = array($pos, $body, $bounce_type, $email, $subject, $xheader, $remove, $rule_no, $rule_cat, $totalFetched);
			return call_user_func_array(array($this, 'callbackAction'), $params);
		}
		
		//var_dump('process bounce-end ' . (mktime() - $time));
		return true;
	}

	public function output($msg)
	{

		$this->messages[] = $msg;
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
	function callbackAction($msgnum, $body, $bounce_type, $email, $subject, $xheader, $remove, $rule_no=false, $rule_cat=false, $totalFetched=0)
	{

//	if ($subject == 'Undelivered Mail Returned to Sender') {
//		var_dump($bounce_type, $subject, $body);
//	}	
		// Check if this is a bounced mail
		if (empty($bounce_type)) {
			return false;
		}

		$nid = null;
		$sid = null;
		$lid = null;


		// Lets get the Application header to ensure that this is the body of sent newsletter
		if (!preg_match_all('/X-Application:\s*Migur\s*Newsletter/is', $body, $matches)) {
			return false;
		}
		
		// Lets get the IDs from headers placed in body of bounced letter
		if (preg_match_all('/X-Subscriber-Id:\s*([0-9]+)/is', $body, $matches)) {
			if (!empty($matches[1][0])) {
				$sid = $matches[1][0];
			}
		}

		if (preg_match_all('/X-Newsletter-Id:\s*([0-9]+)/is', $body, $matches)) {
			if (!empty($matches[1][0])) {
				$nid = $matches[1][0];
			}
		}

		if (preg_match_all('/X-List-Id:\s*([0-9]+)/is', $body, $matches)) {
			if (!empty($matches[1][0])) {
				$lid = $matches[1][0];
			}
		}

		if (empty($nid) && empty($sid)) {
			return false;
		}

		$this->bounceds[] = (object) array(
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

	
	/**
	 * Tries to find last parsed mail.
	 * If found then return position number of it in mailbox
	 * else returns 0
	 * 
	 * @return int
	 * 
	 * @since 1.0.3
	 */
	protected function _getStartPosition($min = 1, $max = null)
	{
		if ($max === null) {
			$max = $this->protocol->getMessagesCount();
			NewsletterHelper::logMessage('Mailbox.Get start position. Total count:'.$max, 'mailbox/');
		}

		// Finaly we found it
		if ($max - $min == 0) {	return $min; } 
		if ($max - $min == 1) {	return $min; } 
		
		$position = $min + floor(($max - $min) / 2);
		
		
		$headers = $this->protocol->getMessageHeaderStructure($position);
		//$headers = @$this->protocol->getMessageHeaders($position);
		
		if (preg_match('/^Message\-ID\:\s*(\<[^\>]+\>)$/im', $headers, $match)) {
			$messageId = $match[1];
		}
				
		if (preg_match('/^Date\:\s*(.+)\s*$/im', $headers, $match)) {
			$date = strtotime(trim($match[1], "\r\n"));
		}

		NewsletterHelper::logMessage('Mailbox.Get start position. Hit to '.$position.'('.$min.'-'.$max.'),date:'.$date, 'mailbox/');

		// In first phase we find first cached message id
		if ($date > $this->getLastDate()) {
			$max = $position;
		} else {
			$min = $position;
		}

		return $this->_getStartPosition($min, $max);
	}

	
	/**
	 * Checks if the Message-ID is present in the chache array
	 * 
	 * @param string $messageId Message-ID header with '<>'
	 * 
	 * @return boolean
	 * 
	 * @since 1.0.3
	 */
	public function checkCache($messageId) 
	{
		return 
			(in_array($messageId, $this->mailboxProfile['data']['ids']) &&
			$this->useCache == true);
	}

	
	/**
	 * Clears message-ids cache
	 * 
	 * @since 1.0.3
	 */
	public function clearCache() 
	{
		$this->mailboxProfile['data'] = array(
			'lastDate' => null,
			'ids' => array()
		);
	}

	
	/** 
	 * Add NEW message id to array cache
	 *
	 * @param string $messageId Message-ID header with '<>'
	 * 
	 * @since 1.0.3
	 */
	public function addToCache($messageId)
	{
		/** Add to cache */
		if (!in_array($messageId, $this->mailboxProfile['data']['ids'])) {
			array_push($this->mailboxProfile['data']['ids'], $messageId);
		}	
	}

	/** 
	 * Set timestamp of a date
	 *
	 * @param string $messageId Message-ID header with '<>'
	 * 
	 * @since 1.0.3
	 */
	public function setLastDate($date)
	{
		/** Add to cache */
		$this->mailboxProfile['data']['lastDate'] = $date;
	}

	
	public function getLastDate()
	{
		return (int)$this->mailboxProfile['data']['lastDate'];
	}
	
	/**
	 * Set error message
	 * 
	 * @param type $error
	 * 
	 * @since 1.0.3
	 */
	public function setError($error){
		$this->error = $error;
	}
	
	
	/**
	 * Get error message
	 * 
	 * @return string Error message
	 * 
	 * @since 1.0.3
	 */
	public function getLastError(){
		return $this->error;
	}
}
