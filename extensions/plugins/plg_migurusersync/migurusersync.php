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
JLoader::import('helpers.subscriber', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter', '');

/**
 * Migur Parameter catcher
 *
 * @package		Joomla
 * @subpackage	Migur
 * @since 		1.6
 */
class plgUserMigurusersync extends JPlugin
{

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
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_migurusersync', JPATH_ADMINISTRATOR, null, false, false);
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
		$params = json_decode(JPluginHelper::getPlugin('user', 'migurusersync')->params);
		
		if ($params->jtocomadd == 0) {
			return;
		}
		
		// Check if component is present
		$newsletter = JComponentHelper::getComponent('com_newsletter');
		
		$user = (object)$user;
		
		if (empty($newsletter) || empty($user->id)) {
			return;
		}
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'tables');
		
		$subscriber = JTable::getInstance('Subscriber', 'NewsletterTable');

		// Try to find by email
		
		
		if (
			$subscriber->load(array('email' => $user->email)) && 
			!empty($subscriber->user_id) && 
			$user->id!=$subscriber->user_id
		){

			JFactory::getApplication()->enqueueMessage(
				JText::_('PLG_MIGURUSERSYNC').": ".JText::sprintf('PLG_MIGURUSERSYNC_SUBSCRIBER_WITH_THIS_EMAIL_BOUND', $user->email), 
				'error'
			);
			return;
		}

		// Try search by id. May be J! user changed the email.
		if (empty($subscriber->subscriber_id)) {
			$subscriber->load(array('user_id' => $user->id));
		}	

		// If user is absent in Subscribers then add. In other case do nothing.
		if (empty($subscriber->subscriber_id)) {
			$subscriber->save(array(
				'name' => !empty($user->name)? $user->name : $user->username,
				'email' => $user->email,
				'state' => 1,
				'html'  => 1,
				'user_id' => $user->id,
				'created_on' => date('Y-m-d H:i:s'),
				'created_by' => 0,
				'modified_on' => '0000-00-00 00:00:00',
				'modified_by' => 0,
				'locked_on' => '0000-00-00 00:00:00',
				'locked_by' => 0,
				'confirmed' => 1,
				'subscription_key' => ''
			));

			$subscriber->save(array(
				'subscription_key' => SubscriberHelper::createSubscriptionKey($subscriber->subscriber_id)
			));
		} else {
			$subscriber->save(array(
				'name' => !empty($user->name)? $user->name : $user->username,
				'email' => $user->email,
				'user_id' => $user->id,
			));	
		}
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
	public function onUserAfterDelete($user, $succes, $msg)
	{
		$params = json_decode(JPluginHelper::getPlugin('user', 'migurusersync')->params);
		
		if ($params->jtocomdelete == 0) {
			return;
		}

		// Check if component is present
		$newsletter = JComponentHelper::getComponent('com_newsletter');
		
		$user = (object)$user;
		
		if (empty($newsletter) || empty($user->id)) {
			return;
		}
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'tables');
		
		$subscriber = JTable::getInstance('Subscriber', 'NewsletterTable');

		// Try to find by email
		
		if (!$subscriber->load(array('user_id' => $user->id))) {
			$subscriber->load(array('email' => $user->email));
		}	
		
		// If user is absent in Subscribers then add. In other case do nothing.
		if (!empty($subscriber->subscriber_id)) {
			$subscriber->delete();
		}
	}
}

