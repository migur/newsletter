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
class plgSystemMiguruserreg extends JPlugin
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
		// Check if component is absent or disabled
		if (JComponentHelper::getParams('com_newsletter')) {
			$this->_disabled = true;
			return;
		}	

		// Import component's constants
		require_once JPATH_ADMINISTRATOR . 
			DIRECTORY_SEPARATOR . 'components' .
			DIRECTORY_SEPARATOR . 'com_newsletter' .
			DIRECTORY_SEPARATOR . 'constants.php';
		
		parent::__construct($subject, $config);
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_miguruserreg', JPATH_ADMINISTRATOR, null, false, false);

		JLoader::import('helpers.autoload', COM_NEWSLETTER_PATH_ADMIN);
		NewsletterHelperAutoload::setup();
		
		JLoader::import('helpers.plugin', COM_NEWSLETTER_PATH_ADMIN);
		JLoader::import('models.automailing.manager', COM_NEWSLETTER_PATH_ADMIN);
		MigurPluginHelper::prepare();
	}


	/** 
	 * Is used to get of a user's groups.
	 * 
	 * @param type $user
	 * @param type $isnew
	 * @param type $success
	 * @param type $msg 
	 */
	public function onUserAfterSave($user)
	{
		if ($this->_disabled) return true;

		// Try to determine what we should do...
		$option	= JRequest::getVar('option', '');
		$task	= JRequest::getVar('task', '');

		
		if ($option == 'com_users') {
			if ($task == 'registration.register') {
				$this->_usersRegistrationRegister($user);
			}
			
			if ($task == 'registration.confirm') {
				$this->_usersRegistrationRegister($user);
			}
		}
	}
	
	protected function _usersRegistrationRegister($user)
	{
		$config = JPluginHelper::getPlugin('plg_miguruserreg');
		
		$lid = $config->get('listid');
		
		// Get models
		$subscriberModel = MigurModel::getInstance('Subscriber', 'NewsletterModel');
		$listModel = MigurModel::getInstance('List', 'NewsletterModel');
		
		// Just for creation of a uid-sid relation 
		$subscriber = $subscriberModel->getItem(array('user_id' => $user['id']));
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
}

