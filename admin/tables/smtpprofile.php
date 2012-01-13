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
class NewsletterTableSmtpprofile extends MigurJTable
{

	const SMTP_DEFAULT = -1;
	
	const JOOMLA_PROFILE_ID = -2;
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
				'#__newsletter_smtp_profiles',
				'smtp_profile_id',
				$_db
		);
	}
	
	
	/**
	 * Overload behavior to store entity with id=0 (Joomla default smtp)
	 * 
	 * @param type $src
	 * @param type $orderingFilter
	 * @param type $ignore
	 * @return type 
	 */
	public function save($src, $orderingFilter = '', $ignore = '')
	{
		if ($src['smtp_profile_id'] == 0) {
			$src['smtp_profile_id'] = NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID;
		}
		
		if ($this->smtp_profile_id == 0) {
			$this->smtp_profile_id = NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID;
		}
		
		$res = parent::save($src, $orderingFilter, $ignore);
		
		if ($this->smtp_profile_id == NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID) {
			$this->smtp_profile_id = 0;
		}
		
	}

	/**
	 * Overload behavior to load entity with id=0 (Joomla default smtp)
	 * 
	 * @param type $src
	 * @param type $orderingFilter
	 * @param type $ignore
	 * @return type 
	 */
	public function load($keys = null, $reset = true)
	{
		if (is_array($keys) && $keys['smtp_profile_id'] == 0) {
			$keys['smtp_profile_id'] = NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID;
		} elseif (!is_array($keys) && !is_object($keys) && $keys == 0) {
			$keys = NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID;
		}
		
		$res = parent::load($keys, $reset);
		
		if ($this->smtp_profile_id == NewsletterTableSmtpprofile::JOOMLA_PROFILE_ID) {
			$this->smtp_profile_id = 0;
		}	
		
		return $res;
	}
}

