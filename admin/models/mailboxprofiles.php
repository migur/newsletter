<?php

/**
 * The SMTPprofiles list model. Implements the standard functional for SMTPprofiles list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.entity.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of SMTPprofiles list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelMailboxprofiles extends MigurModelList
{
	/**
	 * Get all Mailbox profiles
	 */
	public function getAllItems()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__newsletter_mailbox_profiles');
		$db->setQuery($query);
		
		$arr = $db->loadObjectList();
		
		foreach($arr as &$item){
			if (!empty($item->password)) {
				$item->password = base64_decode($item->password);
			}
		}
		
		return $arr;
	}
}
