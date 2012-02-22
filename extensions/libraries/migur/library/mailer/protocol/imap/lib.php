<?php

class MigurMailerProtocolImapLib extends BounceMailHandler 
{
	
	public $errors = array();
	
	public function __construct($options) {

		// testing examples
		$this->action_function    = 'callbackAction'; // default is 'callbackAction'
		$this->verbose            = VERBOSE_QUIET; //VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
		//$this->use_fetchstructure = true; // true is default, no need to speficy
		//$this->testmode           = true; // false is default, no need to specify
		//$this->debug_body_rule    = true; // false is default, no need to specify
		//$this->debug_dsn_rule     = true; // false is default, no need to specify
		//$this->purge_unprocessed  = false; // false is default, no need to specify

		/*
		 * for local mailbox (to process .EML files)
		 */
		//$this->openLocalDirectory('/home/email/temp/mailbox');
		//$this->processMailbox();

		/*
		 * for remote mailbox
		 */
		$this->mailhost           = $options['mailbox_server']; // your mail server
		$this->mailbox_username   = $options['username']; // your mailbox username
		$this->mailbox_password   = $options['password']; // your mailbox password
		$this->port               = $options['mailbox_port']; // the port to access your mailbox, default is 143
		$this->service            = $options['mailbox_server_type']; // the service to use (imap or pop3), default is 'imap'
		$this->service_option     = $options['is_ssl']; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
		$this->boxname            = 'INBOX'; // the mailbox to access, default is 'INBOX'
		$this->noValidateCert     = !empty($options['novalidate-cert']); // do not validate certificates from TLS/SSL server, needed if server uses self-signed certificates
		//$this->moveHard           = true; // default is false
		//$this->hardMailbox        = 'INBOX.hardtest'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
		//$this->moveSoft           = true; // default is false
		//$this->softMailbox        = 'INBOX.softtest'; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
		//$this->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'

		
	}
	
	/**
	 * Clear all IMAP notices and warnings
	 */
	public function __destruct()
	{
		imap_errors(); 
		imap_alerts();
	}
	
	
	/**
	 * Connect to POP3/IMAP Mailbox profile using standard IMAP library
	 * 
	 * @param array $mailbox Sarver parameters and user's credentials
	 * @return boolean 
	 */
	public function connect() 
	{
		@$this->openMailbox();
		
		if (empty($this->_mailbox_link)) {
			return false;
		}
		
		return true;
	}
	

	/**
	 * Deletes the letter from inbox
	 * 
	 * @param integet/string $mid The number of letter in mailbox 
	 * 
	 * @return boolean
	 */
	public function deleteMail($mid, $uid = 0) 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (!empty($this->_mailbox_link)) {
			return @imap_delete($this->_mailbox_link, $mid, $uid);
		}	
		return false;
	}

	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function close() 
	{
		// TODO: Move this method to the Migur library. 
		// It is not relayed to com_newsletter explicitly
		if (!empty($this->_mailbox_link)) {
			
			// Supress all errors and messages
			$res = imap_close($this->_mailbox_link);
		}	
		
		return $res;
	}
	
	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function getLastError() 
	{
		$this->error = imap_last_error(); 
		
		return $this->error;
	}
	
	/**
	 * Closes the mailbox
	 * 
	 * @return boolean
	 */
	public function getMailResource() 
	{
		return $this->_mailbox_link; 
	}
	

	/**
	 * Get messages number
	 * 
	 * @return integer
	 */
	public function getMessagesCount() 
	{
		return imap_num_msg($this->_mailbox_link);
	}
	
	/**
	 * Get body structure
	 * 
	 * @return mixed
	 */
	public function getMessageBodyStructure($idx, $uid = 0) 
	{
		return imap_fetchstructure($this->_mailbox_link, $idx, $uid);
	}
	
	/**
	 * Get body structure
	 * 
	 * @return mixed
	 */
	public function getMessageBodyStruct($pos, $no, $uid = 0) 
	{
		return imap_bodystruct($this->_mailbox_link, $pos, $no, $uid);
	}
	
	
	/**
	 * Get header structure
	 * 
	 * @return mixed
	 */
	public function getMessageHeaderStructure($idx, $uid = 0) 
	{
		return imap_fetchheader($this->_mailbox_link, $idx, $uid);
	}
	

	/**
	 * Get body
	 * 
	 * @return mixed
	 */
	public function getMessageBody($idx, $uid = 0) 
	{
		return imap_body($this->_mailbox_link, $idx, $uid);
	}

	/**
	 * Get body
	 * 
	 * @return mixed
	 */
	public function getMessageFetchedBody($idx, $sec, $uid = 0) 
	{
		return imap_fetchbody($this->_mailbox_link, $idx, $sec, $uid);
	}
	
	/**
	 * Get headers
	 * 
	 * @return mixed
	 */
	public function getMessageHeaders($idx) 
	{
		return imap_header($this->_mailbox_link, $idx);
	}
	
	public function setTimeout($secs) {
		
		imap_timeout(IMAP_OPENTIMEOUT, $secs); 
		imap_timeout(IMAP_CLOSETIMEOUT, $secs); 
		imap_timeout(IMAP_READTIMEOUT, $secs); 
		imap_timeout(IMAP_WRITETIMEOUT,$secs);
	}
	
	/**
	 * Get headers
	 * 
	 * @return mixed
	 */
	public function moveMessage($idx, $hardMailbox) 
	{
		return imap_mail_move($this->_mailbox_link, $idx, $hardMailbox);
	}		


	public function sort($mode, $reverse = 0)
	{
		return imap_sort($this->_mailbox_link, $mode, $reverse, SE_UID);
	}


	public function since($date)
	{
		return imap_search($this->_mailbox_link, 'ALL SINCE "1 November 2011"', SE_UID);
	}

	public function findBy($name, $value)
	{
		return imap_search($this->_mailbox_link, strtoupper($name).' "'.$value.'"', SE_UID);
	}

  /**
   * Open a mail box
   * @return boolean
   */
  function openMailbox() {
    // before starting the processing, let's check the delete flag and do global deletes if true
    if ( trim($this->deleteMsgDate) != '' ) {
      //echo "processing global delete based on date of " . $this->deleteMsgDate . "<br />";
      $this->globalDelete($nameRaw);
    }
    // disable move operations if server is Gmail ... Gmail does not support mailbox creation
    if ( stristr($this->mailhost,'gmail') ) {
      $this->moveSoft = false;
      $this->moveHard = false;
    }
    $port = 
		$this->port . '/' . 
		$this->service . 
		(($this->service_option != 'none')? '/' . $this->service_option : '') . 
		(($this->noValidateCert)? '/novalidate-cert' : '');
	
	$met = ini_get('max_execution_time');
	if ($met < 30 && $met != 0) { set_time_limit(30); }	
	
	$this->setTimeout(20);
	
    if (!$this->testmode) {
      $this->_mailbox_link = imap_open("{".$this->mailhost.":".$port."}" . $this->boxname,$this->mailbox_username,$this->mailbox_password,CL_EXPUNGE, 1);
    } else {
      $this->_mailbox_link = imap_open("{".$this->mailhost.":".$port."}" . $this->boxname,$this->mailbox_username,$this->mailbox_password, NIL, 1);
    }

    if (!$this->_mailbox_link) {
      $this->error_msg = 'Cannot create ' . $this->service . ' connection to ' . $this->mailhost . $this->bmh_newline . 'Error MSG: ' . imap_last_error();
      $this->output();
      return false;
    } else {
      $this->output('Connected to: ' . $this->mailhost . ' (' . $this->mailbox_username . ')');
      return true;
    }
  }

  /**
   * Open a mail box in local file system
   * @param string $file_path (The local mailbox file path)
   * @return boolean
   */
  function openLocal($file_path) {
	  
	$met = ini_get('max_execution_time');
	if ($met < 30 && $met != 0) { set_time_limit(30); }	
	
	$this->	setTimeout(20);
	
    if (!$this->testmode) {
      $this->_mailbox_link = imap_open("$file_path",'','',CL_EXPUNGE);
    } else {
      $this->_mailbox_link = imap_open("$file_path",'','');
    }
    if (!$this->_mailbox_link) {
      $this->error_msg = 'Cannot open the mailbox file to ' . $file_path . $this->bmh_newline . 'Error MSG: ' . imap_last_error();
      $this->output();
      return false;
    } else {
      $this->output('Opened ' . $file_path);
      return true;
    }
  }
	
	
  /**
   * Function to check if a mailbox exists
   * - if not found, it will create it
   * @param string  $mailbox        (the mailbox name, must be in 'INBOX.checkmailbox' format)
   * @param boolean $create         (whether or not to create the checkmailbox if not found, defaults to true)
   * @return boolean
   */
  function isMailboxExist($mailbox) {
    if ( trim($mailbox) == '' || !strstr($mailbox,'INBOX.') ) {
      // this is a critical error with either the mailbox name blank or an invalid mailbox name
      // need to stop processing and exit at this point
      //echo "Invalid mailbox name for move operation. Cannot continue.<br />\n";
      //echo "TIP: the mailbox you want to move the message to must include 'INBOX.' at the start.<br />\n";
      return false;
    }
    $port = $this->port . '/' . $this->service . (($this->service_option != 'none')? '/' . $this->service_option : '');
    $mbox = imap_open('{'.$this->mailhost.":".$port.'}',$this->mailbox_username,$this->mailbox_password,OP_HALFOPEN);
    $list = imap_getmailboxes($mbox,'{'.$this->mailhost.":".$port.'}',"*");
    $mailboxFound = false;
    if (is_array($list)) {
      foreach ($list as $key => $val) {
        // get the mailbox name only
        $nameArr = split('}',imap_utf7_decode($val->name));
        $nameRaw = $nameArr[count($nameArr)-1];
        if ( $mailbox == $nameRaw ) {
          $mailboxFound = true;
        }
      }
//      if ( ($mailboxFound === false) && $create ) {
//        @imap_createmailbox($mbox, imap_utf7_encode('{'.$this->mailhost.":".$port.'}' . $mailbox));
//        imap_close($mbox);
//        return true;
//      } else {
//        imap_close($mbox);
//        return false;
//      }
    } else {
      imap_close($mbox);
      return false;
    }
  }
  
  /**
   * Output additional msg for debug
   * @param string $msg,  if not given, output the last error msg
   * @param string $verbose_level,  the output level of this message
   */
  function output($msg=false,$verbose_level=VERBOSE_SIMPLE) {
    if ($this->verbose >= $verbose_level) {
      if (empty($msg)) {
        $this->errors[] = $this->error_msg . $this->bmh_newline;
      } else {
        $this->errors[] = $msg . $this->bmh_newline;
      }
    }
  }
  
  
  
  /**
   * Set an option
   */
  public function setOption($name, $value) {
	  $this->{$name} = $value;
  }
  

  
  /**
   * Get an option
   */
  public function getOption($name) {
	  return $this->{$name};
  }
  
}