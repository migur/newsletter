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

		// User is exsit. 
		// No need to work with its type
		if (!empty($data['subscriber_id'])) {
			$form->removeField('type');
			unset($data['type']);
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
	 * Override this to add ability to save info about J! user too.
	 * 
	 * @param int $data
	 * 
	 * @return object
	 */
	public function save($data)
	{
		$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		$isJUser = (!empty($data['type']) && $data['type'] == 2);
		
		return $model->save($data, $isJUser);
	}
	
	
	/**
	 * Override this to add ability to get info about J! user too.
	 * 
	 * @param int $pk
	 * 
	 * @return object
	 */
	public function getItem($pk = null)
	{
		$pk	= (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
		
		$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		if(!$model->load($pk)) {
			return false;
		}

		return JArrayHelper::toObject($model->toArray(), 'JObject');
	}
	
	
	/**
	 * Override this to add ability to delete JUsers user too.
	 * 
	 * @param int $data
	 * 
	 * @return object
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$pks		= (array) $pks;
		$table		= $this->getTable();
		$jUser		= JTable::getInstance('user');
		$model		= JModel::getInstance('Subscriber', 'NewsletterModelEntity');

		
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {
			$model->load($pk);
			
			$pk = $model->getId();
			
			if ($table->load($pk)) {

				$context = $this->option.'.'.$this->name;

				// Trigger the onContentBeforeDelete event.
				$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
				if (in_array(false, $result, true)) {
					$this->setError($table->getError());
					return false;
				}
				// Delete from subscriber's table
				if (!$table->delete($pk)) {
					$this->setError($table->getError());
					return false;
				}

				// Delete J! User if it present
				if ($model->user_id && !$jUser->delete($model->user_id)) {
					$this->setError($jUser->getError());
					return false;
				}
				
				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger($this->event_after_delete, array($context, $table));

			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();
		return true;
	}
}
