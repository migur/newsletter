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
	 * Convert 'data' to json.
	 * 
	 * @param array $src - array of data
	 * @param type $orderingFilter
	 * @param type $ignore
	 * @return boolean
	 */
	public function save($src, $orderingFilter = '', $ignore = '')
	{
		if (empty($src['data'])) {
			$src['data'] = array();
		}	
		
		$src['data'] = json_encode($src['data']);
		
		return parent::save($src, $orderingFilter, $ignore);
	}
}

