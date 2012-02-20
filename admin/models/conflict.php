<?php

/**
 * The conflict model. Implements the standard functional for conflict view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of the conflict model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelConflict extends JModelAdmin
{
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
	public function getTable($type = 'Conflict', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$form = $this->loadForm('com_newsletter.conflict', 'conflict', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
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
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.conflict.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}
	
	
	
	/**
	 * Gets all subscriber-user rows.
	 * Swithes all connections of subscriber to a user:
	 * - history, - lists
	 * Deletes all conflicted subscribers
	 * 
	 * @param type $cids Subscribers ids
	 */
	public function mergePreservingUsers($cids)
	{
		$dbo = JFactory::getDbo();
		
		// Gets all subscriber-user rows.
		$query = $dbo->getQuery(true); 
		$query->select('s.subscriber_id AS subId, u.id AS userId');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('', '#__users AS u ON s.email = u.email');
		$query->where('s.subscriber_id in(' . implode(',', $cids) . ')');
		
		$dbo->setQuery($query);
		$res = $dbo->loadObjectList();
		
		// Process each row.
		$user = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		$sub = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		foreach($res as $row) {
			
			// Load user
			if(!$user->load('-'.$row->userId)) {
				return false;
			}

			// Load sub
			if(!$sub->load($row->subId)) {
				return false;
			}

			// Merge history additional data and history from sub to user
			$dbo->setQuery(
				'UPDATE #__newsletter_sub_history SET subscriber_id = '.$user->getId().' '.
				'WHERE subscriber_id = '.$sub->getId());
			if (!$dbo->query()) {
				return false;
			}

			
			// Merge lists...
			
			// Load all subscriber's lists
			$query = $dbo->getQuery(true); 
			$query->select('DISTINCT list_id');
			$query->from('#__newsletter_sub_list AS s');
			$query->where('subscriber_id ='.$sub->getId());
			$dbo->setQuery($query);
			$res = $dbo->loadAssocList();
			$subLists = array(); foreach($res as $row) { $subLists[] = $row['list_id']; }

			// Load user's lists
			$query = $dbo->getQuery(true); 
			$query->select('DISTINCT list_id');
			$query->from('#__newsletter_sub_list AS s');
			$query->where('subscriber_id ='.$user->getId());
			$dbo->setQuery($query);
			$res = $dbo->loadAssocList();
			$userLists = array(); foreach($res as $row) { $userLists[] = $row['list_id']; }
			
			$lists = array_diff($subLists, $userLists);
			
			
			// Merge list entries
			if (!empty($lists)) {
				
				foreach($lists as $lid) {
					if (!$user->assignToList($lid)) {
						return false;
					}
				}	
			}
			
			// Delete subscriber row
			if (!$sub->delete()) {
				return false;
			}
		}
		
		return true;
	}
	

	
	/**
	 * Gets all subscriber-user rows.
	 * Swithes all connections of subscriber to a user:
	 * - history, - lists
	 * Move all subscriber's data into user
	 * Deletes all conflicted subscribers
	 * 
	 * @param type $cids Subscribers ids
	 */
	public function mergePreservingSubs($cids)
	{
		$dbo = JFactory::getDbo();
		
		// Gets all subscriber-user rows.
		$query = $dbo->getQuery(true); 
		$query->select('s.subscriber_id AS subId, u.id AS userId');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('', '#__users AS u ON s.email = u.email');
		$query->where('s.subscriber_id in(' . implode(',', $cids) . ')');
		
		$dbo->setQuery($query);
		$res = $dbo->loadObjectList();
		
		// Process each row.
		$user = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		$sub = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

		echo $query;
		
		foreach($res as $row) {
			
			// Load user
			if(!$user->load('-'.$row->userId)) {
				return false;
			}

			// Load sub
			if(!$sub->load($row->subId)) {
				return false;
			}

			// Merge history additional data and history from sub to user
			$dbo->setQuery(
				'UPDATE #__newsletter_sub_history SET subscriber_id = '.$user->getId().' '.
				'WHERE subscriber_id = '.$sub->getId());
			if (!$dbo->query()) {
				return false;
			}

			// Merge lists...
			
			// Load all subscriber's lists
			$query = $dbo->getQuery(true); 
			$query->select('DISTINCT list_id');
			$query->from('#__newsletter_sub_list AS s');
			$query->where('subscriber_id ='.$sub->getId());
			$dbo->setQuery($query);
			$res = $dbo->loadAssocList();
			$subLists = array(); foreach($res as $row) { $subLists[] = $row['list_id']; }

			// Load user's lists
			$query = $dbo->getQuery(true); 
			$query->select('DISTINCT list_id');
			$query->from('#__newsletter_sub_list AS s');
			$query->where('subscriber_id ='.$user->getId());
			$dbo->setQuery($query);
			$res = $dbo->loadAssocList();
			$userLists = array(); foreach($res as $row) { $userLists[] = $row['list_id']; }
			
			$lists = array_diff($subLists, $userLists);
			
			// Merge list entries
			if (!empty($lists)) {
				
				foreach($lists as $lid) {
					if (!$user->assignToList($lid)) {
						return false;
					}
				}	
			}
			
			// Move all data from subscriber into Joomla user
			$subData = $sub->toArray();
			unset($subData['subscriber_id']);
			unset($subData['email']);
			unset($subData['user_id']);
			$user->save($subData, 'isJUser');
			
			// Delete subscriber row
			if (!$sub->delete()) {
				return false;
			}
		}
		
		return true;
	}	

	
	
	/**
	 * Gets all subscriber-user rows.
	 * Swithes all connections connections of subscriber to a user:
	 * - history, - lists
	 * Deletes all conflicted subscribers
	 * 
	 * @param type $cids Subscribers ids
	 */
	public function deleteSubscribers($cids)
	{
		$dbo = JFactory::getDbo();
		
		// Gets all subscriber-user rows.
		$query = $dbo->getQuery(true); 
		$query->select('s.subscriber_id AS subId, u.id AS userId');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('', '#__users AS u ON s.email = u.email');
		$query->where('s.subscriber_id in(' . implode(',', $cids) . ')');
		
		$dbo->setQuery($query);
		$res = $dbo->loadObjectList();
		
		// Process each row.
		$sub = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		
		foreach($res as $row) {
			
			// Load user
			if(!$sub->load($row->subId)) {
				return false;
			}
			
			// Delete subscriber row
			if (!$sub->delete()) {
				return false;
			}
		}
		
		return true;
	}
}
