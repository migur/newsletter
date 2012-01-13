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
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.application.component.modellist');

/**
 * Class extends the functionality of JModelList
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurModel extends JModel
{
	
	protected $_defaults = array();
	
	protected $_data = null;

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

		$this->_data = (object) $data;
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
		$table = $this->getTable();
		$res = $table->load($data);

		if ($res) {
			$this->setFromArray($table->getProperties());
		}

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
			$this->setFromArray($data);
		}

		$data = $this->toArray();

		$table = $this->getTable();
		
		// If you provide the PK then this metod perform update.
		// If all ok then method return new Id
		if($table->save($this->toArray())) {
			$this->setFromArray($table->getProperties());
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

}

