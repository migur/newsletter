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
JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscriber model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSubscriber extends JModelAdmin
{

	protected $_context;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 *
	 * @return	JTable	A database object
	 * @since	1.0.4
	 */
	public function getTable($type = 'Subscriber', $prefix = 'NewsletterTable')
	{
		return parent::getTable($type, $prefix);
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

		if($form->getValue('user_id') > 0) {
			$form->setFieldAttribute('name', 'readonly', 'true');
			$form->setFieldAttribute('email', 'readonly', 'true');
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
	 * Save user and check if it has a subscription key
	 * 
	 * @param array|object $data
	 * @return boolean 
	 */
	public function save($data, $fastStore = false, $tableInstance = null)
	{
		// In some cases we need to store data in fastest way.
		// Without triggering of events and checking some cases
		// So use $fastStore to do it.

		$table = ($tableInstance instanceof JTable)? $tableInstance : $this->getTable();
		$table->subscriber_id = null;
		
		$isNew = empty($data['subscriber_id']);

		if (!empty($data['user_id'])) {
			unset($data['name']);
			unset($data['email']);
		}
		
		// If this will be the new record then let's set default values
		if ($isNew) {
			
			$data['user_id'] = 0;
			$data['modified_on'] = 0;
			$data['modified_by'] = 0;
			$data['locked_on'] = 0;
			$data['locked_by'] = 0;

			if (!isset($data['created_by'])) {
				$data['created_by'] = JFactory::getUser()->id;
			}
			
			if (!isset($data['html'])) {
				$data['html'] = (int) NewsletterHelperData::getDefault('html', 'subscriber');
			}
			
			if (!isset($data['state'])) {
				$data['state'] = (int) NewsletterHelperData::getDefault('state', 'subscriber');
			}
			
			if (!isset($data['created_on'])) {
				$data['created_on'] = date('Y-m-d H:i:s');
			}
		}	

		if ($fastStore) {
			
			if (!$table->save($data)) {
				return false;
			}
			$sid = $table->subscriber_id;
			
		} else {
			
			if (!parent::save($data)) {
				return false;
			}
			$sid = $this->getState($this->getName() . '.id');
		}
		
		if ($isNew) {
			
			$data['subscriber_id'] = $sid;
			$data['subscription_key'] = $this->_createSubscriptionKey($data['subscriber_id']);
			
			if (empty($data['confirmed'])) {
				$data['confirmed'] = $data['subscription_key'];
			}
			
			return $table->save($data);
		}	
		
		return true;
	}
	
	/**
	 * Override this to add ability to get info about J! user too.
	 * 
	 * Can find by 
	 *	- NUMERIC - assumes that it is subscriber_id
	 *  - subscriber_id - in subscribers table only
	 *  - name - first in subscribers. if fail then in jusers
	 *  - email - first in subscribers. if fail then in jusers
	 *  - subscription_key - in subscribers table only
	 *  - user_id - in juser's table only
	 *
	 * If J!user found but the relational subscriber row is absent then 
	 * automaticaly create a subscriber row.
	 * 
	 * @param numeric|array
	 *  
	 * @return array
	 */
	public function getItem($data = null, $tableInstance = null)
	{
		
		if (is_numeric($data)) {
			$data = array('subscriber_id' => (int)$data);
		}

		if (empty($data) && $this->getState($this->getName() . '.id')) {
			$data = array('subscriber_id' => (int) $this->getState($this->getName() . '.id'));
		}
		
		if (empty($data)) {
			return false;
		}

		// There may be some cases...
		$juserData = null;
		$subData = null;
	
		$dbo = $this->getDbo();

		// Try to find in subscribers table...
		$row = array();
		
		// Remain only fields for SUBSCRIBERS table
		$fieldsAvailable = array('subscriber_id', 'name', 'email', 'subscription_key');
		$filter = array();
		foreach($data as $name => $val) {
			if (in_array($name, $fieldsAvailable)) $filter[$name] = $val;
		}
		
		// If there are some fields then try to find
		if (!empty($filter)) {
		
			$query = $dbo->getQuery(true);
			$query->select(
					"u.id as juser_id, " . 
					"u.name as juser_name, " . 
					"u.username as juser_username, " . 
					"u.email as juser_email, " .
					"u.password as juser_password, " . 
					"u.usertype as juser_usertype, " . 
					"u.block as juser_block, " . 
					"u.sendEmail as juser_sendEmail, " . 
					"u.registerDate as juser_registerDate, " . 
					"u.lastvisitDate as juser_lastvisitDate, " . 
					"u.activation as juser_activation, " . 
					"u.params as juser_params, " . 
					"s.* ");

			$query->from('#__newsletter_subscribers AS s');
			$query->join('LEFT', '#__users AS u ON s.user_id = u.id');
		
			foreach($filter as $name => $val) {
				$query->where("s.$name=".$dbo->quote($val));
			}
		
			//echo $query; die;
			
			// Do the request
			$dbo->setQuery($query);
			$row = $dbo->loadAssoc();
		}	
		
		
		// If we cannot find subscriber then try to find j!user
		if (empty($row)) {
			
			// Remain only fields for SUBSCRIBERS table
			$fieldsAvailable = array('user_id', 'name', 'email');
			$filter = array();
			foreach($data as $name => $val) {
				if (in_array($name, $fieldsAvailable)) $filter[$name] = $val;
			}

			// If there are some fields then try to find
			if (!empty($filter)) {
			
				$query = $dbo->getQuery(true);
				$query->select(
						"u.id as juser_id, " . 
						"u.name as juser_name, " . 
						"u.username as juser_username, " . 
						"u.email as juser_email, " .
						"u.password as juser_password, " . 
						"u.usertype as juser_usertype, " . 
						"u.block as juser_block, " . 
						"u.sendEmail as juser_sendEmail, " . 
						"u.registerDate as juser_registerDate, " . 
						"u.lastvisitDate as juser_lastvisitDate, " . 
						"u.activation as juser_activation, " . 
						"u.params as juser_params, " . 
						"s.* ");

				$query->from('#__newsletter_subscribers AS s');
				$query->join('RIGHT', '#__users AS u ON s.user_id = u.id');
				foreach($filter as $name => $val) {
					if ($name == 'user_id') $name = 'id';
					$query->where("u.$name=".$dbo->quote($val));
				}

				//echo $query; die;
				// Do the request
				$dbo->setQuery($query);
				$row = $dbo->loadAssoc();
			}	
		}

		// If we cant find nothing then return false.
		if (empty($row)) {
			return false;
		}	

		
		$table = ($tableInstance instanceof JTable)? $tableInstance : $this->getTable();
		$table->subscriber_id = null;
		
		// Ok. Found something. If this was a J!user and 
		// he does not have subscriber row then create it!
		if (!empty($row['juser_id']) && empty($row['subscriber_id'])) {

			// Prepare for new record
			$table->subscriber_id = null;
			
			// Fill...
			$row['name'] = '';
			$row['email'] = '';
			$row['user_id'] = $row['juser_id'];
			$row['created_by'] = JFactory::getUser()->id;
			$row['html'] = (int) NewsletterHelperData::getDefault('html', 'subscriber');
			$row['state'] = (int) NewsletterHelperData::getDefault('state', 'subscriber');
			$row['created_on'] = 0;
			$row['modified_on'] = 0;
			$row['modified_by'] = 0;
			$row['locked_on'] = 0;
			$row['locked_by'] = 0;
			
			//..and save
			$table->save($row);
			$row['subscriber_id'] = $table->subscriber_id;
		}

		// Fix problem with 'subscription_key' if it is there
		if(empty($row['subscription_key'])) {
			$row['subscription_key'] = $this->_createSubscriptionKey($row['subscriber_id']);
			$willSave = true;
		}

		// Fix problems with 'confirmed' if it is there
		if(empty($row['confirmed'])) {
			$row['confirmed'] = $row['subscription_key'];
			$willSave = true;
		}

		// Save fixes if needed
		if (!empty($willSave)) {
			$table->save($row);	
			$row = array_merge($row, $table->getProperties(true));
		}	
		
		// Make available J!user's data as subscriber's data
		if (!empty($row['juser_name'])) {
			$row['name'] = $row['juser_name'];
		}
		
		if (!empty($row['juser_email'])) {
			$row['email'] = $row['juser_email'];
		}

		if (!empty($row['juser_registerDate'])) {
			$row['created_on'] = $row['juser_registerDate'];
		}

		// Process params
		if (array_key_exists('params', $row) && is_string($row['params']))
		{
			$registry = new JRegistry;
			$registry->loadString($row['params']);
			$row['params'] = $registry->toArray();
		}

		return $row;
	}

	
	/**
	 * Override this to add ability to get info about J! user too.
	 * 
	 * @param int $pk
	 * 
	 * @return object
	 */
	public function getJItem($data)
	{
		return $this->getTable('Users', 'JTable')->load($data);
	}
	
	public function isExist($options)
	{
		$res = array();
		$table = $this->getTable('Users', 'JTable');
		$table->load($data);
		$res['juser_id'] = $table->id;
		
		$table = $this->getTable('Subscriber', 'NewsletterTable');
		$table->load($data);
		$res['subscriber_id'] = $table->subscriber_id;
		
		return $res;
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
		$model		= MigurModel::getInstance('Subscriber', 'NewsletterModelEntity');

		
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

				// Trigger the onContentAfterDelete event.
				$dispatcher->trigger('onMigurAfterSubscriberDelete', array(
					'subscriberId' => $pk));
				
			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();
		return true;
	}
	
	/**
	 * Creates the subscription key. Use user id and random number
	 * Length of subscription key is 15 characters
	 *
	 * @param  $userId - integer. The user ID.
	 *
	 * @return string
	 * @since 1.0
	 */
	protected function _createSubscriptionKey($sid)
	{
		if (empty($sid)) {
			return false;
		}

		// to get the constant length
		$mask = '000000000';
		$id = substr($mask, 0, strlen($mask) - strlen($sid)) . $sid;
		return rand(100000, 999999) . $id . time();
	}

	
	/**
	 * Get type of newsletter user prefer to recieve
	 */
	public function getType($subscriber)
	{
		if (is_numeric($subscriber)) {
			$subscriber = $this->getTable()->load($subscriber);
		}
		
		$subscriber = (array) $subscriber;
		
		return ($subscriber['html'] == 1) ? 'html' : 'plain';
	}
}
