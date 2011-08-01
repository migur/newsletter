<?php

/**
 * The subscriber table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of subscriber table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableJExtension extends MigurJTable
{
	/**
	 * The meaning of values in "type" table field.
	 */
	const TYPE_MODULE = '1';
	const TYPE_PLUGIN = '2';

	/**
	 * The constructor of a class.
	 *
	 * @param	object	$config		An object of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(&$_db)
	{
		parent::__construct(
				'#__extensions',
				'extension_id',
				$_db
		);
	}

}

