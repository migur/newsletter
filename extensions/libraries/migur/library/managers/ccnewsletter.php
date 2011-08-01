<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
jimport('migur.library.managers.common');

class ccNewsletterManager extends commonManager
{

	public $name = 'ccNewsletter';

	/**
	 * Fetch the lists from acyMailer component to array
	 *
	 * @return array - array of objects
	 * @since  1.0
	 */
	public function exportLists()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('s.email, s.name, "" AS list_name, s.sdate AS created');
		$query->from('#__ccnewsletter_subscribers AS s');
		$query->order('s.email');
		// Set the query
		$db->setQuery($query);
		$objs = $db->loadObjectList();

		return (array) $objs;
	}

	/**
	 * Check the structure of a exported/imported tables
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	function isValid()
	{

		/*
		  CREATE TABLE IF NOT EXISTS `#__ccnewsletter_subscribers` (
		  `id` int(11) NOT NULL auto_increment,
		  `name` text NOT NULL,
		  `email` text NOT NULL,
		  `plainText` tinyint(1) NOT NULL default '0',
		  `enabled` tinyint(1) NOT NULL default '1',
		  `sdate` datetime NOT NULL default '0000-00-00 00:00:00',
		  `lastSentNewsletter` int(11) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY (`lastSentNewsletter`)
		  ) TYPE=MyISAM  DEFAULT CHARSET=utf8 ; */

		// Check the subscribers table
		return $this->validateTable(
			'#__ccnewsletter_subscribers',
			array('name', 'email', 'sdate')
		);
	}

}
