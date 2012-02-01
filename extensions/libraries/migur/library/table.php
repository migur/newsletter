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
	
	/**
	 * Pre-save processing. 
	 * Convert 'params' to json. Encode password.
	 * 
	 * @param $updateNulls See JTable
	 * 
	 * @return boolean
	 */
	public function store($updateNulls = false) 
	{
		if (isset($this->params)) {
			$buff = $this->params;
			$this->paramsToJson();
		}	
		
		$res = parent::store($updateNulls = false);
		
		if (isset($buff)) {
			$this->params = $buff;
		}
		
		return $res;
	}
	
	
	/**
	 * Converts array|object to json
	 * 
	 * @param array|object $this->params
	 * 
	 * @return string Encoded entity 
	 */
	public function paramsToJson() 
	{
		if (!is_string($this->params)) {

			if (is_object($this->params) && get_class($this->params) == 'JObject') {
				$this->params = $this->params->getProperties();
			}

			// If the source value is an object, get its accessible properties.
			if (is_object($this->params)) {
				$this->params = get_object_vars($this->params);
			}
			
			if (is_array($this->params) || is_object($this->params)) {
				$this->params = json_encode($this->params);
			}
		}
		
		return $this->params;
	}	
	
	
	/**
	 * Converts json string to array|object
	 * 
	 * @param string $params
	 * 
	 * @return string Decoded entity 
	 */
	public function paramsFromJson() 
	{
		if (empty($this->params)) {
			$this->params = array();
		}
		
		if (is_string($this->params)) {
			$this->params = (array)json_decode($this->params, true);
		}
		
		return $this->params;
	}	
}
