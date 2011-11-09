<?php

/**
 * The newsletterext model. Implements the standard functional for extensions view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of the extensions list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelNewsletterext extends JModel
{

	protected $_context;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'Newsletterext', $prefix = 'NewsletterTable', $config = array())
	{
		return MigurJTable::getInstance($type, $prefix, $config);
	}

	/**
	 *  Re set all of extensions to the newsletter
	 *
	 * @param array $data  array of extension objects
	 * @param int   $id    the newsletter id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function rebindExtensions($data, $id)
	{
		$table = $this->getTable();
		if ($table->deleteBy(array('newsletter_id' => $id))) {

			if (!empty($data) ) {
				foreach ($data as $item) {
					
					if (isset($item->type)) {

						// Skip the empty plugins (not configured)
						if ($item->type == 2 && empty($item->params)) continue;

						$table->reset();
						$table->set($table->getKeyName(), null);

						$item->newsletter_id = $id;
						$table->bind($item);
						$table->store();
					}	
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * Get all extensions of the newsletter. Proxy for table method.
	 *
	 * @param array $data  array of extension objects
	 * @param int   $id    the newsletter id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function getExtensionsBy($id)
	{
		$table = $this->getTable();
		return $table->getExtensionsBy($id);
	}

}
