<?php

/**
 * The template table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of template table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableTemplate extends MigurTable
{

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
				'#__newsletter_template_styles',
				't_style_id',
				$_db
		);
	}


	/**
	 * Overriden JTable::store to set created/modified and user id.
	 *
	 * @param	boolean	True to update fields even if they are null.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		// Verify that the alias is unique
		$table = JTable::getInstance('Template', 'NewsletterTable');

		if (isset($this->name)) {
			if ($table->load(array('name' => $this->name))) {

				$this->setError(JText::_('COM_NEWSLETTER_DATABASE_ERROR_SUBSCRIBER_UNIQUE_NAME'));
				return false;
			}
		}
		return parent::store($updateNulls);
	}
}

