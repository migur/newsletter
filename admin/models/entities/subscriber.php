<?php

/**
 * The SMTP profile model. Implements the standard functional for SMTP profile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

/**
 * Class of SMTPprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelEntitySubscriber extends MigurModel
{
	public function getType() {
		
		return ($this->html == 1) ? 'html' : 'plain';
	}
	

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
		return JTable::getInstance($type, $prefix);
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
	protected function _createSubscriptionKey()
	{
		$sid = $this->_data->subscriber_id;
		// to get the constant length
		$mask = '000000000';
		$id = substr($mask, 0, strlen($mask) - strlen($sid)) . $sid;
		$this->_data->subscription_key = rand(100000, 999999) . $id . time();
	}
	
	
	/**
	 * Shorthand for creation.
	 * 
	 * @param type $data
	 * @return type 
	 */
	public function create($data) 
	{
		$data = (array)$data;
		
		if (empty($data['user_id'])) {
			$data['user_id'] = 0;
		}	
		
		$data['created_on'] = date('Y-m-d H:i:s');
		$data['created_by'] = $data['user_id'];
		$data['modified_on'] = 0;
		$data['modified_by'] = 0;

		if ($this->save($data)) {
			
			$this->_data->confirmed = (empty($data['confirmed']) || $data['confirmed'] != 1)?
				$this->_data->subscription_key : 1;
			
			return $this->save();
		}	
			
		return false;
	}
	
	
	public function save($data = array()) 
	{
		if(!parent::save($data)) {
			return false;
		}
		
		if (empty($this->_data->subscription_key)) {
			$this->_createSubscriptionKey();
			return $this->save();
		}
		
		return true;
	}
	
	/**
	 * Method to check if user is already binded to the list.
	 *
	 * @param	int|string $data The id a list.
	 *
	 * @return	object on success, false or null on fail
	 * @since	1.0
	 */
	public function isInList($lid)
	{
		return $this->getTable('sublist')
			->load(array(
				'subscriber_id' => (int)$this->_data->subscriber_id,
				'list_id' => (int)$lid));
	}

	
	/**
	 * Get confirmed status
	 * 
	 * @return boolean
	 */
	public function isConfirmed() 
	{
		return $this->_data->confirmed == 1;
	}
	
	/**
	 * Bind current subscriber to list.
	 *
	 * @param	array	$data	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function assignToList($lid)
	{
		if (empty($lid)) {
			return false;
		}
		
		// Load the row. If it exists then nothing to do
		if ($this->isInList($lid)) {
			return true;
		}

		// Save and finish.
		return $this->getTable('sublist')
			->save(array(
				'subscriber_id'    => (int)$this->_data->subscriber_id,
				'list_id'          => (int)$lid,
				'confirmed'        => $this->_data->confirmed));
	}
	
	public function confirm() 
	{
		$this->_data->confirmed = 1;
		
		if (!$this->save()) {
			return false;
		}
		
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__newsletter_sub_list set confirmed=1 WHERE confirmed=" . $db->quote($this->_data->subscription_key));
		return $subscriber = $db->query();
	}
}
