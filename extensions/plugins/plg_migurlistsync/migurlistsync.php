<?php

/**
 * Migur Ajax Search Plugin
 * 
 * @version		$Id: migursearch.php $
 * @package		Joomla
 * @subpackage	System
 * @copyright	Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Migur Parameter catcher
 *
 * @package		Joomla
 * @subpackage	Migur
 * @since 		1.6
 */
class plgSystemMigurlistsync extends JPlugin
{

	protected $_disabled = false;

	protected $_task = '';
	
	protected $_userGroupsBefore = array();
	
	protected $_rules = array();
	
	/**
	 * Plugin that enhances the core Joomla! Search field and gives Ajax results
	 * 
	 * @param $subject
	 * @param $config - array - config data of plugin
	 * 
	 * @return void
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		// Check if component is present
		$bootstrap = JPATH_ADMINISTRATOR . 
			DIRECTORY_SEPARATOR . 'components' . 
			DIRECTORY_SEPARATOR . 'com_newsletter' . 
			DIRECTORY_SEPARATOR . 'bootstrap.php';
		
		if (!file_exists($bootstrap)) {
			$this->_disabled = true;
			return;
		}	
		
		$newsletter = JComponentHelper::getComponent('com_newsletter');
		if (empty($newsletter)) {
			$this->_disabled = true;
			return;
		}	

		require_once $bootstrap;
		
		MigurComNewsletterBootstrap::initAutoloading();
		MigurComNewsletterBootstrap::initEnvironment();
		
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_migurlistsync', JPATH_ADMINISTRATOR, null, false, false);

		JLoader::import('helpers.plugin', COM_NEWSLETTER_PATH_ADMIN);
		JLoader::import('models.automailing.manager', COM_NEWSLETTER_PATH_ADMIN);
		NewsletterHelperPlugin::prepare();
	}


	/** 
	 * Is used to get of a user's groups.
	 * 
	 * @param type $user
	 * @param type $isnew
	 * @param type $success
	 * @param type $msg 
	 */
	public function onAfterRoute()
	{
		if ($this->_disabled) return true;

		// Try to determine what we should do...
		$this->_task = JRequest::getVar('task', '');
		
		// Catch the moment when J! is about to process the collection of users.
		// We cannot get users's groups AFTER processing because
		// controller uses redirect. As onAfterRoute is 
		// the latest event before redirection, so we must do all stuff here.
		// Not fair that we do it BEFORE J! process but we cannot do it another way.
		if ($this->_task == 'user.batch') {
			
			$users = JRequest::getVar('cid', array());
			$form = JRequest::getVar('batch', array());
			$groupId = !empty($form['group_id'])? $form['group_id'] : '';
			$event = !empty($form['group_action'])? $form['group_action'] : '';
			
			$dbo = JFactory::getDbo();
			$query = $dbo->getQuery(true);
			$query
				->select('DISTINCT u.*, GROUP_CONCAT(DISTINCT um.group_id SEPARATOR ",") AS groups')
				->from('#__users AS u')
				->join('left', '#__user_usergroup_map AS um ON u.id = um.user_id')
				->where('u.id IN (' . implode(',', $users) . ')')
				->group('u.id');
			$dbo->setQuery($query);
			
			$users = $dbo->loadObjectList();
			
			// Let's speed up this script!
			$db = JFactory::getDbo();
			$transactionItemsCount = 0;
			$db->setQuery('SET AUTOCOMMIT=0;');
			$db->query();
			
			foreach($users as $user) {
				
				$userGroupsBefore = explode(',', $user->groups);

				switch($event) {
					case 'add':
						$acquireds = array_diff(array($groupId), $userGroupsBefore);
						$losses = array();
						break;
					
					case 'del':
						$acquireds = array();
						$losses = array_intersect($userGroupsBefore, array($groupId));
						break;
						
					case 'set':	
						$acquireds = array_diff(array($groupId), $userGroupsBefore);
						$losses = array_diff($userGroupsBefore, array($groupId));
						break;
				};
			
				if (!empty($acquireds) || !empty($losses)) {
					$this->_processUser($user, false, $acquireds, $losses);
				}	
			}
			
			// Commit it all!
			$db->setQuery('COMMIT;');
			$db->query();
			$db->setQuery('SET AUTOCOMMIT=1;');
			$db->query();
		}	
	}
	
	
	/** 
	 * Is used to determine previous state of a user's groups
	 * 
	 * @param type $user
	 * @param type $isnew
	 * @param type $success
	 * @param type $msg 
	 */
	public function onUserBeforeSave($user, $isnew, $success)
	{
		if ($this->_disabled) return true;
		
		$this->_userGroupsBefore = array_values($user['groups']);
	}

	
	/**
	 * Executes on every storage of J! user. 3 cases:
	 * - create new subscriber if subscriber with user->id and with user->email not exists
	 * - bind J! user to subscriber if subscriber with user->email exists and has user_id=0
	 * - updates data in bound subscriber
	 * 
	 * Raises an error if subscriber has user->email but user_id != user->id
	 * 
	 * @param JUser $user
	 * @param boolean $isnew
	 * @param boolean $success
	 * @param string $msg
	 * 
	 * @return void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if ($this->_disabled || !$success) return true;
		
		// Check if user is valid
		$user = (object) $user;
		if (empty($user->id)) return;
		
		$userGroupsAfter = array_values($user->groups);
		
		$acquireds = (array) $this->_getAcquiredGroups($this->_userGroupsBefore, $userGroupsAfter);
		$losses = (array) $this->_getLostGroups($this->_userGroupsBefore, $userGroupsAfter);
		
		
		return $this->_processUser($user, $isnew, $acquireds, $losses);
	}


	/**
	 * Executes on every deletion of J! user.
	 * - deletes binded subscriber by user->id == user_id
	 * - or deletes subscriber by user->email
	 * 
	 * @param JUser $user
	 * @param boolean $isnew
	 * @param boolean $success
	 * @param string $msg
	 * 
	 * @return void
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if ($this->_disabled || !$success) return true;

		// Check if user is valid
		$user = (object) $user;
		if (empty($user->id)) return;

		$dbo = JFactory::getDbo();
		$dbo->setQuery('DELETE FROM #__newsletter_subscribers WHERE user_id ='. (int) $user->id);
		$dbo->query();
	}

	
	protected function _processUser($user, $isnew, $acquireds, $losses)
	{	
		// Now we need to process 3 types of events:
		// on_register_user
		// on_add_user_to_group
		// on_remove_user_from_group
		
		// On register user...
		if ($isnew) {
			$rules = $this->_getRules('on_register_user');
			$rules = $this->_filterRules($rules, $acquireds);
			$this->_processActions($user, $rules);
		}
		
		// On on_add_user_to_group...
		if (count($acquireds) > 0) {
			$rules = $this->_getRules('on_add_user_to_group');
			$rules = $this->_filterRules($rules, $acquireds);
			$this->_processActions($user, $rules);
		}
		
		// On on_remove_user_from_group...
		if (count($losses) > 0) {
			$rules = $this->_getRules('on_remove_user_from_group');
			$rules = $this->_filterRules($rules, $losses);
			$this->_processActions($user, $rules);
		}
	}
	
	
	/**
	 * Handle all found rules.
	 * 
	 * @param type $user
	 * @param type $rules 
	 */
	protected function _processActions($user, $rules) 
	{
		// Foreach all and perform actions...
		foreach($rules as $ev) {

			// ADD action...
			if ($ev->action == 'add') {
				$this->_addUserToList($user, $ev->list_id);
			}
			// REMOVE is not in option for new user...
			if ($ev->action == 'remove') {
				$this->_removeUserFromList($user, $ev->list_id);
			}
		}
	}		
	
	
	/**
	 * 1. Creates uid-sid relation if needed.
	 * 2. Assigns subscriber to list
	 * 3. Adds SIGNEDUP record to user's history
	 * 4. Triggers event for automailing.
	 * 
	 * @param type $user User object
	 * @param type $lid List id
	 * 
	 */
	protected function _addUserToList($user, $lid)
	{
		// Get models
		$subscriberModel = MigurModel::getInstance('Subscriber', 'NewsletterModel');
		$listModel = MigurModel::getInstance('List', 'NewsletterModel');
		
		// Just for creation of a uid-sid relation 
		$subscriber = $subscriberModel->getItem(array('email' => $user->email));
		$sid = $subscriber['subscriber_id'];
		
		$list = $listModel->getItem($lid);

		$listModel->assignSubscriber($lid, $subscriber, array('confirmed' => true));

		// Add to history all subscriptions
		$history = JTable::getInstance('history', 'NewsletterTable');
		$history->save(array(
			'subscriber_id' => $sid,
			'list_id' => $lid,
			'newsletter_id' => NULL,
			'action' => NewsletterTableHistory::ACTION_SIGNEDUP,
			'date' => date('Y-m-d H:i:s'),
			'text' => addslashes($list->name)
		));
		unset($history);

		// Triggering the subscribed plugins.
		// Process automailing via internal plugin plgMigurAutomail
		JFactory::getApplication()->triggerEvent(
			'onMigurAfterSubscribe', 
			array(
				'subscriberId' => $sid,
				'lists' => array($lid))
		);
	}

	/**
	 * 1. Creates uid-sid relation if needed.
	 * 2. Unbind subscriber from list
	 * 3. Adds UNSUBSCRIBED record to user's history
	 * 4. Triggers event for automailing.
	 * 
	 * @param type $user User object
	 * @param type $lid List id
	 * 
	 */
	protected function _removeUserFromList($user, $lid)
	{
		// Get models
		$subscriberModel = MigurModel::getInstance('Subscriber', 'NewsletterModel');
		$listModel = MigurModel::getInstance('List', 'NewsletterModel');
		
		// Just for cration of a uid-sid relation 
		$subscriber = $subscriberModel->getItem(array('email' => $user->email));
		$sid = $subscriber['subscriber_id'];
		
		$list = $listModel->getItem($lid);

		$listModel->unbindSubscriber($lid, $sid);

		// Add to history all subscriptions
		$history = JTable::getInstance('history', 'NewsletterTable');
		$history->save(array(
			'subscriber_id' => $sid,
			'list_id' => $lid,
			'newsletter_id' => NULL,
			'action' => NewsletterTableHistory::ACTION_UNSUBSCRIBED,
			'date' => date('Y-m-d H:i:s'),
			'text' => addslashes($list->name)
		));
		unset($history);

		// Triggering the subscribed plugins.
		// Process automailing via internal plugin plgMigurAutomail
		JFactory::getApplication()->triggerEvent(
			'onMigurAfterUnsubscribe', 
			array(
				'subscriberId' => $sid,
				'lists' => array($lid))
		);
	}
	
	/**
	 * Get groups from which user has been removed
	 * 
	 * @param type $before array
	 * @param type $after array
	 * @return type array
	 */
	protected function _getLostGroups($before, $after)
	{
		return array_diff($before, $after);
	}

	
	/**
	 * Get groups to which user has been added
	 * 
	 * @param type $before array
	 * @param type $after array
	 * @return type array
	 */
	protected function _getAcquiredGroups($before, $after)
	{
		return array_diff($after, $before);
	}
	
	
	protected function _getRules($event)
	{
		if (!isset($this->_rules[$event])) {
			// Get rules
			$dbo = JFactory::getDbo();
			$query = $dbo->getQuery(true);
			$query->select('*')->from('#__newsletter_list_events')->where('event="' . $event . '"');
			$dbo->setQuery($query);
			$this->_rules[$event] = $dbo->loadObjectList();
		}
		
		return $this->_rules[$event];
	}


	protected function _filterRules($rules, $groups)
	{
		$res = array();
		foreach($rules as $rule) {
			if (in_array($rule->jgroup_id, $groups)) {
				$res[] = $rule;
			}	
		}
		
		return $res;
	}
}

