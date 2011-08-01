<?php

/**
 * The subscriber model. Implements the standard functional for subscriber view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscriber model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSubscriber extends JModelAdmin
{

	protected $_context;
	protected $_tableInstance = array();

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
	public function getTable($type = 'Subscriber', $prefix = 'NewsletterTable', $config = array(), $isNew = false)
	{
		if ($isNew) {
			return JTable::getInstance($type, $prefix, $config);
		} else {
			if (empty($this->_tableInstance[$type]) || !is_object($this->_tableInstance[$type])) {
				$this->_tableInstance[$type] = JTable::getInstance($type, $prefix, $config);
			}
			return $this->_tableInstance[$type];
		}
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_newsletter.subscriber', 'subscriber', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		if (!empty($data)) {
			$form->bind($data);
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.subscriber.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string Script files
	 * @since  1.0
	 */
	public function getScript()
	{
		return 'administrator/components/com_newsletter/models/forms/subscriber.js';
	}

	/**
	 * Method to check if user is already binded to the list.
	 *
	 * @param	array	$data	The ids of subscriber and list.
	 *
	 * @return	object on success, false or null on fail
	 * @since	1.0
	 */
	public function isInList($data)
	{
		if (!empty($data->subscriber_id) && !empty($data->list_id)) {
			// Initialise variables;
			$table = $this->getTable('sublist');

			// Load the row if saving an existing record.
			return $table->load(array(
				'subscriber_id' => $data->subscriber_id,
				'list_id' => $data->list_id
			));
		}

		return false;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	$data	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function assignToList($data)
	{
		if (!empty($data->subscriber_id) && !empty($data->list_id)) {
			// Initialise variables;
			$table = $this->getTable('sublist');
			// Allow an exception to be throw.
			try {
				// Load the row. If it exists then nothing to do
				// Bind the data.
				if ($this->isInList($data)) {
					return true;
				}

				$table->reset();
				$table->set($table->getKeyName(), null);

				if (!$table->bind($data)) {
					$this->setError($table->getError());
					return false;
				}

				// Store the data.
				if (!$table->store()) {
					$this->setError($table->getError());
					return false;
				}

				// Clean the cache.
				$cache = JFactory::getCache($this->option);
				$cache->clean();
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Method to unbind the subscriber from list.
	 *
	 * @param	array	$data	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function unbindFromList($data)
	{
		if (!empty($data->subscriber_id) && !empty($data->list_id)) {

			// Initialise variables;
			$table = $this->getTable('sublist');

			// Allow an exception to be throw.
			try {
				$table->reset();
				$table->set($table->getKeyName(), null);

				// Load the row. If it doesn't exists then nothing to do
				if (!$this->isInList($data)) {
					return true;
				}


				// Bind the data.
				if (!$table->bind($data)) {
					$this->setError($table->getError());
					return false;
				}

				// Store the data.
				if (!$table->delete()) {
					$this->setError($table->getError());
					return false;
				}

				// Clean the cache.
				$cache = JFactory::getCache($this->option);
				$cache->clean();
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}

			return true;
		}

		return false;
	}

	public function save($data)
	{

		if (parent::save($data)) {
			if ($this->getState('subscriber.new')) {
				$id = $this->getState('subscriber.id');
				$table = $this->getTable();
				$table->load($id);
				// No confirmation need if we create the subscriber from admin
				$table->confirmed = 1;
				// Current date
				$table->created_on = date('Y-m-d H:i:s');
			
				$table->subscription_key = SubscriberHelper::createSubscriptionKey($id);
				
				$table->store();
			}
			return true;
		}
		return false;
	}

}
