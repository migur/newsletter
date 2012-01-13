<?php

/**
 * The cron controller file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.automailing.manager', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('migur.library.mailer');

/*
 * Class of the Subscribe controller.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterControllerSubscribe extends JController
{

	/**
	 * The constructor of a class
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * The method to bind subscriber to J! user.
	 * To test <b>?option=com_newsletter&task=subscribe.subscribe&newsletter-name=index.php
	 * @return void
	 * @since  1.0
	 */
	public function subscribe()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Get variables from request
		$name = JRequest::getString('newsletter-name', null);
		$email = JRequest::getString('newsletter-email', null);
		$html = (int) JRequest::getInt('newsletter-html', null);
		$lists = DataHelper::toArrayOfInts(JRequest::getVar('newsletter-lists', array()));
		$fbenabled = JRequest::getInt('fbenabled', array());
		//$sendto = JRequest::getVar('sendto');
		
		// Check token, die on error.
		JRequest::checkToken() or jexit('Invalid Token');

		if (empty($name) || empty($email) || !in_array($html, array(0,1)) || empty($lists)) {
			jexit('One or more parameters is missing');
		}

		$comParams = JComponentHelper::getComponent('com_newsletter')->params;


		// Insert into db
		// TODO: Add santiy checks, use model instead
		$db->setQuery("SELECT * FROM #__newsletter_subscribers WHERE email = " . $db->quote($email));
		$subscriber = $db->loadObject();


		$subKey = SubscriberHelper::createSubscriptionKey($subscriber->subscriber_id);
		// Decide need the confirmation or not
		//Get the FB settings
		$fbAppId  = $comParams->get('fbappid');
		$fbSecret = $comParams->get('fbsecret');
		
		$confirmed = $subKey;
		// Let's try to get user data from FB
		if (!empty($fbAppId) && !empty($fbSecret) && !empty($fbenabled)) {
			$me = SubscriberHelper::getFbMe($fbAppId, $fbSecret);
			if (!empty($me->email) && $me->email == $email) {
				$confirmed = 1;
			}
		}


		// If subscriber does not exist before, add it
		if (!isset($subscriber->subscriber_id)) {
			$db->setQuery("INSERT INTO #__newsletter_subscribers(name,email,state,html,user_id,created_on,created_by,modified_on,modified_by)"
				. " VALUES("
				. $db->quote($name) . ", "
				. $db->quote($email) . ", "
				. "1, "
				. $db->quote($html) . ", "
				. $db->quote($user->id) . ", "
				. $db->quote(date('Y-m-d H:i:s')) . ", "
				. $db->quote($user->id) . ", "
				. $db->quote(date('Y-m-d H:i:s')) . ", "
				. $db->quote($user->id)
				. ")"
			);

			$db->query();
			$db->setQuery("SELECT * FROM #__newsletter_subscribers WHERE email = " . $db->quote($email));
			$subscriber = $db->loadObject();

			// Create the subscriber key
			$subKey = SubscriberHelper::createSubscriptionKey($subscriber->subscriber_id);
			if ($confirmed !== 1) {
				$confirmed = $subKey;
			}

			$db->setQuery(
				"UPDATE #__newsletter_subscribers " .
				" SET subscription_key=" . $db->quote($subKey) .
				", confirmed=" . $db->quote($confirmed) .
				" WHERE subscriber_id = " . $db->quote($subscriber->subscriber_id)
			);
			$db->query();

			$subscriber->subscription_key = $subKey;
		}

		// Set user_id for subscriber if this is not set yet
		if (empty($subscriber->user_id) && !empty($user->id)) {
			
			$db->setQuery(
				"UPDATE #__newsletter_subscribers " .
				" SET user_id=" . (int)$user->id .
				" WHERE subscriber_id = " . (int)$subscriber->subscriber_id
			);
			$db->query();
		}
		
		
		// Add subscriptions to lists, ignore if already in db
		foreach ($lists as $list) {
			$db->setQuery(
				"INSERT IGNORE INTO #__newsletter_sub_list SET subscriber_id = " . $subscriber->subscriber_id .
				", list_id = " . $db->quote((int) $list) .
				", confirmed = " . $db->quote($confirmed)
			);
			$db->query();
		}

		
		// Triggering the automailing process.
		$amManager = new NewsletterAutomailingManager();
		$amManager->processSubscription(array(
			'subscriberId' => $subscriber->subscriber_id
		));
		
		// If the email or subscriptions are needed to confirm then send the email
		if ($confirmed == 1) {
 			$message = JText::sprintf('Thank you %s for subscribing to our Newsletter!', $name);
			jexit($message);
		}


		// Let's send the subscription email
		$db->setQuery("SELECT * FROM #__newsletter_lists WHERE list_id in (" . implode(',', $lists) . ')');
		$mysqlObj = $db->loadObjectList();
		
		$titles = array();
		if (!empty($mysqlObj)) {
			foreach ($mysqlObj as $item) {
				$titles[] = $item->name;
			}
		}
		
		PlaceholderHelper::setPlaceholder('list', $titles);

		/* Let's try to determine the wellcoming newsletter to send.
		 *	Now we get the letter from the first list.
		 */
		$newsletterId = !empty($mysqlObj)? (int)$mysqlObj[0]->send_at_reg : 0;
			
		if($newsletterId == 0) {
			/* If the wellcoming letter is not defined
			 *	then try to use the default wellcoming newsletter
			 */
			$newsletterId = (int)$comParams->get('subscription_newsletter_id');
		}

		if ($newsletterId > 0) {
			
			try {
				
				$mailer = new MigurMailer();
				$res = $mailer->send(array(
					'type' => $html? 'html' : 'plain',
					'subscriber' => $subscriber,
					'newsletter_id' => $newsletterId,
					'tracking' => true
				));

				if (!$res->state) {
					jexit('The error was occured. Please try again later');
				}

				$message = JText::sprintf('Thank you %s for subscribing to our Newsletter! You will need to confirm your subscription. There should be an email in your inbox in a few minutes!', $name);
				
			} catch(Exception $e) {
				$message = $e->getMessage();
			}	
			
		} else {

			// TODO: There should be the notification for admin instead.
			JLog::getInstance()->addEntry(array('comment' => 'subscribe.subscribe: The wellcoming newsletter not found'));
			$message = JText::_('The wellcoming newsletter is not defined');
		}

		// Redirect to page
		jexit($message);
		//$this->setRedirect(base64_decode($sendto), $message, 'message');
	}

	/**
	 * The method to bind subscriber to J! user.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function confirm()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Get variables from request
		$subKey = JRequest::getString('id', null);

		if (empty($subKey)) {
			// Redirect to page
			$message = JText::_("The error has occured. Please try again later");
			$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid='.$subKey, $message, 'error');
			return;
		}
		$db->setQuery("SELECT subscriber_id FROM #__newsletter_subscribers WHERE subscription_key=" . $db->quote($subKey));
		$subscriber = $db->loadObject();
		if (count($subscriber) < 1) {
			// Redirect to page
			$message = JText::_("The error has occured. Please try again later");
			$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid='.$subKey, $message, 'error');
			return;
		}
		
		// Insert into db
		// TODO: Add santiy checks, use model instead
		$db->setQuery("UPDATE #__newsletter_subscribers set confirmed=1 WHERE confirmed=" . $db->quote($subKey));
		$subscriber = $db->query();

		$db->setQuery("UPDATE #__newsletter_sub_list set confirmed=1 WHERE confirmed=" . $db->quote($subKey));
		$subscriber = $db->query();

		//die();
		// Redirect to page
		$message = JText::_("Your subscription has confirmed successfully. Thanks!");
		$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid='.$subKey, $message, 'message');

		return true;
	}

	/**
	 * The method to check the input data and render the
	 * lists to unsubscribe.
	 *
	 * @return void
	 * @since  1.0
	 */

	public function showUnsubscribe()
	{
		// Get variables from request
		$uid = JRequest::getString('uid', '');
		$nid = JRequest::getString('nid', '');

		// Check token, die on error.
		//JRequest::checkToken() or jexit('Invalid Token');

		if (empty($uid) || empty($nid)) {
			jexit('One or more parameters is missing');
		}

		JRequest::setVar('view', 'subscribe');
		JRequest::setVar('layout', 'unsubscribe');
		$this->display();

		return true;
	}

	
	/**
	 * The method to unsubscribe the subscriber
	 * from one or more lists.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function unsubscribe()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		// Get variables from request
		$uid   = JRequest::getString('newsletter-uid', '');
		$nid   = JRequest::getString('newsletter-nid', '');
		$lists = JRequest::getVar('newsletter-lists', array());

		// Check token, die on error.
		//JRequest::checkToken() or jexit('Invalid Token');

		if (empty($uid) || empty($lists)) {
			$this->setRedirect(
				JRoute::_('index.php?option=com_newsletter&view=subscribe&layuout=unsubscribe', false),
				JText::_('COM_NEWSLETTER_PARAMETERS_NOT_FOUND'),
				'error');
			return;
		}

		// Insert into db
		// TODO: Add santiy checks, use model instead
		$db->setQuery( "SELECT * FROM #__newsletter_subscribers WHERE subscription_key = " . $db->quote(addslashes($uid)) );
		$subscriber = $db->loadObject();
		if (empty($subscriber->subscriber_id)) {
			$this->setRedirect(
				JRoute::_('index.php?option=com_newsletter&view=subscribe&layuout=unsubscribe', false),
				JText::_('COM_NEWSLETTER_USER_NOT_FOUND'),
				'error');
			return;
		}

		// Check the newsletter if nid is present
		if (!empty($nid)) {
			$db->setQuery( "SELECT newsletter_id FROM #__newsletter_newsletters WHERE newsletter_id = " . $db->quote(addslashes($nid)));
			$newsletter = $db->loadObject();
			if (empty($newsletter->newsletter_id)) {
				$this->setRedirect(
					JRoute::_('index.php?option=com_newsletter&view=subscribe&layuout=unsubscribe', false),
					JText::_('COM_NEWSLETTER_NEWSLETTER_NOT_FOUND'));
				return;
			}
		}	
		$app->triggerEvent(
			'onMigurNewsletterBeforeUnsubscribe', 
			array(
				'subscriber' => $subscriber,
				'lists' => $lists
		));
		
		foreach ($lists as $list) {

			// Delete subscriptions from list
			$db->setQuery(
				"DELETE FROM #__newsletter_sub_list ".
				"WHERE subscriber_id = " . $db->quote((int)$subscriber->subscriber_id) . " AND list_id = " . $db->quote((int)$list)
			);
			$db->query();
			
			// Add to history
			$db->setQuery(
				"INSERT IGNORE INTO #__newsletter_sub_history SET ".
				" newsletter_id=" . $db->quote((int)$nid) . ", ".
				" subscriber_id=" . $db->quote((int)$subscriber->subscriber_id) . ", ".
				" list_id=" . $db->quote((int)$list) . ", ".
				" date=" . $db->quote(date('Y-m-d H:i:s')) . ", ".
				" action=" . $db->quote(NewsletterTableHistory::ACTION_UNSUBSCRIBED) . ", ".
				" text=''"
			);
			$res = $db->query();
		}

		// Process automailing unsubscription
		$amManager = new NewsletterAutomailingManager();
		$amManager->processUnsubscription();
		
		$app->triggerEvent(
			'onMigurNewsletterAfterUnsubscribe', 
			array(
				'subscriber' => $subscriber,
				'lists' => $lists,
				'result' => $res
		));
		
		// Redirect to page
		$this->setRedirect(
			JRoute::_('index.php?option=com_newsletter&view=subscribe&layuout=unsubscribe', false),
			JText::sprintf('COM_NEWSLETTER_THANK_YOU_FOR_USING_SERVICE', $subscriber->name),
			'message');
	}
}

