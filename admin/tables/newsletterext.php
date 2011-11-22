<?php

/**
 * The newsletters_ext table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of newsletters_ext table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableNewsletterext extends MigurJTable
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
				'#__newsletter_newsletters_ext',
				'newsletters_ext_id',
				$_db
		);
	}

	/**
	 * Get all the extensions for newsletter.
	 *
	 * @param  int   $id id of a newsletter
	 * @return array list of extensions
	 */
	public function getExtensionsBy($id)
	{
		$db = $this->_db;
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'a.newsletter_id, a.extension_id, a.position, a.params, a.ordering, ' .
			'a.title, e.extension, e.params AS params_default, e.type, native, showtitle'
		);

		$query->from('#__newsletter_newsletters_ext AS a');
		$query->join('', '#__newsletter_extensions AS e ON a.extension_id = e.extension_id');
		$query->where('newsletter_id=' . intval($id));
		$query->where('native = 0');
		$query->order($db->getEscaped('ordering asc'));

		$db->setQuery($query);
		$modulesCom = $db->loadAssocList();

		$query = $db->getQuery(true);
		$query->select(
			'ne.newsletter_id, e.extension_id, ne.position, ne.params, ne.ordering, '
			.'ne.title, element AS extension, "{}" AS params_default, 1 as type, native, showtitle'
		);
		$query->from('#__extensions AS e');
		$query->join('','#__newsletter_newsletters_ext AS ne ON e.extension_id = ne.extension_id');
		$query->where('e.type = "module"');
		$query->where('ne.newsletter_id = ' . intval($id));
		$query->where('ne.native = 1');

		// Set the query
		$db->setQuery($query);
		$modulesNat = $db->loadAssocList();

		$res = array_merge($modulesCom, $modulesNat);

		foreach ($res as $idx => $item) {
			if (!empty($item['params']) && is_string($item['params'])) {
				$res[$idx]['params'] = json_decode($item['params']);
			}
			$res[$idx]['params'] = (object)$res[$idx]['params'];

			if (!empty($item['params_default']) && is_string($item['params_default'])) {
				$res[$idx]['params_default'] = json_decode($item['params_default']);
			}
			$res[$idx]['params_default'] = (object)$res[$idx]['params_default'];
		}
		return $res;
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
		$query->from('#__newsletter_newsletters_ext AS a');
		$query->where('newsletter_id=' . intval($id));

		$db->setQuery($query);
		//echo nl2br(str_replace('#__','jos_',$query)); die;
 		return $db->loadAssocList();
	}
}

