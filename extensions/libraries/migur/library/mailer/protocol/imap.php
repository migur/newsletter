<?php

/**
 * Wrapper for implementation of POP3 protocol
 */
class MigurMailerProtocolImap {

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

			require_once 'imap'.DS.'lib.php';
			return $this->implementation = new MigurMailerProtocolImapLib($options);
			
		}
			
		// Use this as falback
		require_once 'imap'.DS.'php.php';
		return $this->implementation = new MigurMailerProtocolImapPhp($options);
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