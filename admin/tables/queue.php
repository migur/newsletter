<?php

/**
 * The queue table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of queue table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableQueue extends MigurTable
{

	const STATE_SENT = 0;
	const STATE_INQUEUE = 1;
	const STATE_BOUNCED = 2;


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
				'#__newsletter_queue',
				'queue_id',
				$_db
		);
	}
}

