<?php

/**
 * The smtp_profiles table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of smtp_profiles table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableMailboxprofile extends MigurJTable
{

	const MAILBOX_DEFAULT = -1;

	/**
	 * The constructor of a class.
	 *
	 * @param	object	$config		An object of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$_db)
	{
		parent::__construct(
				'#__newsletter_mailbox_profiles',
				'mailbox_profile_id',
				$_db
		);
	}

	
	/**
	 * Pre-save processing. 
	 * Convert 'data' to json. Encode password.
	 * 
	 * @param array $src - array of data
	 * @param type $orderingFilter
	 * @param type $ignore
	 * @return boolean
	 */
	public function store($updateNulls = false) 
	{
		if (!empty($this->data) && !is_string($this->data)) {
			$this->data = json_encode($this->data);
		}	
		
		if (!empty($this->password)) {
			$this->password = base64_encode($this->password);
		}
		
		return parent::store($updateNulls = false);
	}
	
	
	/**
	 * Post-load processing. Decode password.
	 * 
	 * @param type $keys
	 * @param type $reset
	 * @return type 
	 */
	public function load($keys = null, $reset = true){
		
		if (!parent::load($keys, $reset)) {
			return false;
		};

		if (!empty($this->password)) {
			$this->password = base64_decode($this->password);
		}

		if (!empty($this->data)) {
			$this->data = (array)json_decode($this->data);
		}

		return true;
	}
}

