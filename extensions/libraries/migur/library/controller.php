<?php

/**
 * The extended version of JModelList.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

/**
 * Class extends the functionality of JModelList
 *
 * @since   1.0
 * @package Migur.Newsletter
 * 
 * @deprecated since 12.05
 */
if (!class_exists('JControllerLegacy')) {
	class JControllerLegacy extends JController {}
}

class MigurController extends JControllerLegacy
{
	
	protected $_defaults = array();
	
	protected $_data = null;

	protected $_keyName = null;
	
	public function __construct($config = array())
	{
		$res = parent::__construct($config);
		$this->setFromArray($this->_defaults);
		return $res;
	}

	/**
	 * Return data as array
	 * 
	 * @return object
	 */
	public function toArray()
	{
		return (array) $this->_data;
	}

	/**
	 * Return data as object
	 * 
	 * @return object
	 */
	public function toObject()
	{
		return (object) $this->_data;
	}

	/**
	 * Populate from array
	 * 
	 * @param array|object $data
	 * @return object Link to data 
	 */
	public function setFromArray($data)
	{
		$data = (array) $data;

		if (isset($data['params'])) {
			if (is_string($data['params'])) {
				$data['params'] = json_decode($data['params']);
			}
			$data['params'] = (object) $data['params'];
		}

		$this->_data = (object)$data;
		return $this->_data;
	}

	/**
	 * Add to model data from array
	 * 
	 * @param array|object $data
	 * @return object Link to data 
	 */
	public function addFromArray($data)
	{
		$data = (array) $data;

		if (isset($data['params'])) {
			if (is_string($data['params'])) {
				$data['params'] = json_decode($data['params']);
			}
			$data['params'] = (object) $data['params'];
		}

		$this->_data = (object)array_merge((array)$this->_data, (array)$data);
		return $this->_data;
	}
	
	/**
	 * Load data from storage by id
	 * 
	 * @param type $data Value of PK or array with fields to match
	 * @return object|false
	 */
	public function load($data)
	{
		if (empty($data)) {
			return false;
		}
		
		$table = $this->getTable();
		$res = $table->load($data);
		
		$this->setFromArray($res? $table->getProperties() : array());

		return $res;
	}

	/**
	 * Save data to storage
	 * 
	 * @param type $data
	 * @return true|false
	 */
	public function save($data = array())
	{
		if (!empty($data)) {
			$this->addFromArray($data);
		}

		$table = $this->getTable();
		
		// If you provide the PK then this metod perform update.
		// If all ok then method return new Id
		if($table->save($this->toArray())) {
			$this->addFromArray($table->getProperties());
			return $this->{$table->getKeyName()};
		}
		
		return false;
	}

	/**
	 * Getter for data items
	 * 
	 * @param type $name
	 * @return type 
	 */
	public function __get($name)
	{
		return isset($this->_data->$name) ? $this->_data->$name : null;
	}

	/**
	 * Setter for data items
	 * 
	 * @param type $name
	 * @return type 
	 */
	public function __set($name, $val)
	{
		$this->_data->$name = $val;

		return $this->_data->$name;
	}
	
	
	public function getId() 
	{
		$keyName = $this->_getKeyName();

		if (empty($this->_data->{$keyName})) {
			return null;
		}
		
		return $this->_data->{$keyName};
	}
	
	/**
	 * Tries to find ID in data
	 * 
	 * @param type $data 
	 */
	public function extractId($data) 
	{
		if (is_string($data) || is_numeric($data)) {
			return (string)$data;
		}
		
		$data = (array)$data;
		$keyName = $this->_getKeyName();
		
		return isset($data[$keyName])? $data[$keyName] : null;
	}
	
	
	/**
	 * Gets an ID from table
	 * 
	 * @return type 
	 */
	protected function _getKeyName() 
	{
		if (empty($this->_keyName)) {
		
			$table = $this->getTable();
			$this->_keyName = $table->getKeyName();
			unset($table);
		}	

		return $this->_keyName;
	}
	
	
	
	/**
	 * 
	 * 
	 */
	public function delete()
	{
		$table = $this->getTable();
		
		if (!$this->getId() || !$table->delete($this->getId())) {
			return false;
		}
		
		return true;
	}
}

