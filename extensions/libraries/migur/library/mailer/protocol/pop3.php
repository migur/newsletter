<?php

/**
 * Wrapper for implementation of POP3 protocol
 */
class MigurMailerProtocolPop3 {

	/**
	 * Pointer to real implementation of protocol
	 */
	protected $implementation = null;
	
	/**
	 * Constructor.
	 * 
	 * @param array $options Settings to connect to server
	 * @return object 
	 */
	public function __construct($options) {
		
		if (function_exists('imap_open')) {
			
			// The Phpmailer_BMH can handle both imap and pop. 
			// No need to implement MigurMailerProtocolPOPLib
			require_once 'imap'.DS.'lib.php';
			return $this->implementation = new MigurMailerProtocolImapLib($options);
		}
			
		// Use this as falback
		require_once 'pop3'.DS.'php.php';
		return $this->implementation = new MigurMailerProtocolPop3Php($options);
	}
	
	/**
	 * Proxies all calls to implementaition.
	 * 
	 * @param string $name
	 * @param array $args
	 * @return mixed 
	 */
	public function __call($name, $args) {
		
		return call_user_func_array(array($this->implementation, $name), $args);
	}
}
