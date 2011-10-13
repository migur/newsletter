<?php

/**
 * The sent table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of downloads table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableDownloads extends JTable
{
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
				'#__newsletter_downloads',
				'downloads_id',
				$_db
		);
	}
	
	
	/**
	 * Get all the extensions for newsletter.
	 *
	 * @param  int   $id id of a newsletter
	 * @return array list of extensions
	 */
	public function getRowsBy($id)
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from('#__newsletter_downloads AS a');
		$query->where('newsletter_id=' . intval($id));

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','jos_',$query)); die;
 		return $db->loadAssocList();
	}
	
}

