<?php

/**
 * The download helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class DownloadHelper
{

	/**
	 * Get the files attached to a newsletter data by newsletter id.
	 *
	 * @param  string - id of a letter
	 *
	 * @return mixed  - bool false on fail, the array of objects of success
	 * @since  1.0
	 */
	static function getByNewsletterId($nId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__newsletter_downloads AS d');
		$query->where('d.newsletter_id = "' . (int) $nId . '"');
		// Set the query
		$db->setQuery($query);
		$files = $db->loadObjectList();

		if (!empty($files)) {
			foreach ($files as &$item) {
				self::getAttributes($item);
			}
		}
		return (array) $files;
	}

	/**
	 * Get the file attributes attached.
	 *
	 * @param  object  - pointer to a file object
	 *
	 * @return boolean - sucsess
	 * @since  1.0
	 */
	static function getAttributes($item)
	{
		$prefix = JPATH_ROOT;
		$file = $prefix . DS . $item->filename;
		if (!file_exists($file)) {
			$item->size = null;
			$item->type = null;
			return false;
		}

		$item->size = filesize($file);
		$ext = explode('.', $item->filename);
		$item->type = (count($ext) > 0) ? $ext[count($ext) - 1] : '';

		return true;
	}

}
