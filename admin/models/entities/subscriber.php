<?php

/**
 * The SMTP profile model. Implements the standard functional for SMTP profile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of SMTPprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelEntitySubscriber extends MigurModel
{
	public function getType() {
		
		return ($this->html == 1) ? 'html' : 'plain';
	}
	

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 *
	 * @return	JTable	A database object
	 * @since	1.0.4
	 */
	public function getTable($type = 'Subscriber', $prefix = 'NewsletterTable')
	{
		return JTable::getInstance($type, $prefix);
	}
}
