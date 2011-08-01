<?php

/**
 * Extends the functionality of JTable.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.database.table');

/**
 * Class for extending the functionality of the JTable
 *
 * @since   1.0
 * @package Migur.Newsletter
 */

class MigurJTable extends JTable
{

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param	mixed	An optional primary key value to delete.  If not set the
	 * 			instance property value is used.
	 * @return	boolean	True on success.
	 * @since	1.0
	 * */
	public function deleteBy($params = null)
	{
		if (!empty($params) && !is_array($params)) {
			return parent::delete($params);
		}

		// If no primary key is given, return false.
		if (empty($params)) {
			$e = new JException(JText::_('MIGUR_PARAMS_IS_ABSENT'));
			$this->setError($e);
			return false;
		}

		// Delete the row by primary key.
		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from($this->_tbl);
		$fields = array_keys($this->getFields());
		//print_r($fields);

		foreach ($params as $name => $val) {
			if (in_array($name, $fields)) {
				$query->where($name . ' = ' . $this->_db->quote($val));
			} else {
				$e = new JException(JText::_('MIGUR_THE_FIELD_NOT_IN_THE_TABLE'));
				$this->setError($e);
				return false;
			}
		}

		$this->_db->setQuery($query);

		// Check for a database error.
		if (!$this->_db->query()) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return true;
	}

	/**
	 * Add the conversion of params field to JSON when data binds ONLY
	 * if params is ARRAY, OBJECT or JOBJECT
	 *
	 * @param	array $hash named array
	 * 
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since   1.0
	 */
	public function bind($src, $ignore = array())
	{
		// If the source value is not an array or object return false.
		if (!is_object($src) && !is_array($src)) {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
			$this->setError($e);
			return false;
		}

		// If the source value is an object, get its accessible properties.
		if (is_object($src)) {
			$src = get_object_vars($src);
		}

		if (isset($src['params'])) {

			if (!is_string($src['params'])) {

				if (is_object($src['params']) && get_class($src['params']) == 'JObject') {
					$src['params'] = $src['params']->getProperties();
				}

				if (is_array($src['params']) || is_object($src['params'])) {
					$src['params'] = json_encode($src['params']);
				}
			}
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * The simplest vay to add the ARRAY to JSON "params" field.
	 *
	 * @param  array $array - the data to add
	 *
	 * @return string - the result JSON
	 * @since  1.0
	 */
	public function addToParams($array)
	{
		if (empty($this->params)) {
			$this->params = array();
		}
		if (is_string($this->params)) {
			$this->params = (array) json_decode($this->params);
		}
		if (is_object($this->params)) {
			$this->params = (array) $this->params;
		}

		$this->params = json_encode(array_merge($this->params, $array));
		return $this->params;
	}

}
