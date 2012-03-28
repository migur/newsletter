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
		$listsIds = DataHelper::toArrayOfInts(JRequest::getVar('newsletter-lists', array()));
		$fbenabled = JRequest::getInt('fbenabled', array());
		//$sendto = JRequest::getVar('sendto');
		
		// Check token, d_i_e on error.
		JRequest::checkToken() or jexit('Invalid Token');

		if (empty($name) || empty($email) || !in_array($html, array(0,1)) || empty($listsIds)) {
			jexit('One or more parameters is missing');
		}

		$comParams = JComponentHelper::getComponent('com_newsletter')->params;

		$isNew = false;
		
		// Let's check if we can create user as confirmed
		$confirmed = ($comParams->get('users_autoconfirm') == '1');
		
		// try to get user data from FB
		$fbAppId  = $comParams->get('fbappid');
		$fbSecret = $comParams->get('fbsecret');
		if (!empty($fbAppId) && !empty($fbSecret) && !empty($fbenabled)) {
			$me = SubscriberHelper::getFbMe($fbAppId, $fbSecret);
			if (!empty($me->email) && $me->email == $email) {
				$confirmed = true;
			}
		}
		
		// If it is a user's email
		$emailsAreEqual = false;
		if (!empty($user->id) && $user->email == $email) {
			$confirmed = true;
			$emailsAreEqual = true;
		}
		
		
		// Get from db
		$subscriber = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
		$subscriber->load(array('email' => $email));
		
		// If subscriber does not exist then create it
		if (!$subscriber->getId()) {

			$subscriber->create(array(
				'name'  => $name,
				'email' => $email,
				'state' => '1',
				'html' 	=> $html,
				'user_id' => $emailsAreEqual? $user->id : 0,
				'confirmed' => $confirmed));
			
		} else {
			
			// Update subscriber
			if ($confirmed == true) {
				// Confirm subscriber and 
				// ALL ITS ASSIGNINGS TO LISTS
				$subscriber->confirm();
			}	
			
			if ($emailsAreEqual) {
				// If user is authorized
				$subscriber->user_id = $user->id;
				$subscriber->save();
			}	
		}

		
		// Add subscribers to lists, ignore if already in db
        
        
		$assignedListsIds = array();
		foreach ($listsIds as $list) {
			if (!$subscriber->isInList($list)) {
                
				$subscriber->assignToList($list);
				$assignedListsIds[] = $list;
			}
		}

        // Get lists we assigned
        $listManager = JModel::getInstance('Lists', 'NewsletterModel');
        $lists = $listManager->getItemsByIds($assignedListsIds);

        
        // Add to history all subscriptions
        foreach($lists as $list) {
            
            $history = JTable::getInstance('history', 'NewsletterTable');
            $history->save(array(
                'subscriber_id' => $subscriber->getId(),
                'list_id'       => $list->list_id,
                'newsletter_id' => NULL,
                'action'        => NewsletterTableHistory::ACTION_SIGNEDUP,
                'date'          => date('Y-m-d H:i:s'),
                'text'          => addslashes($list->name)
            ));
            unset($history);
        }    
        
        
		// Triggering the automailing process.
		$amManager = new NewsletterAutomailingManager();
		$amManager->processSubscription(array(
			'subscriberId' => $subscriber->subscriber_id
		));

		
		// If subscriber is confirmed then no need to send emails.
		$message = JText::sprintf('Thank you %s for subscribing to our Newsletter!', $name);
		
		if (!$subscriber->isConfirmed()) {
			
			// Let's send newsletters
			$mailer = new MigurMailer();
			foreach($lists as $list) {
				try {
					$newsletter = JModel::getInstance('Newsletter', 'NewsletterModelEntity');
					$newsletter->loadAsWelcoming($list->send_at_reg);
					
					PlaceholderHelper::setPlaceholder('listname', $list->name);
					$res = $mailer->send(array(
						'type'          => $newsletter->isFallback()? 'plain' : $subscriber->getType(),
						'subscriber'    => $subscriber->toObject(),
						'newsletter_id' => $newsletter->newsletter_id,
						'tracking'      => true));
					if($res->state) {
						$message = JText::sprintf('Thank you %s for subscribing to our Newsletter! You will need to confirm your subscription. There should be an email in your inbox in a few minutes!', $name);

						LogHelper::addMessage(
							'COM_NEWSLETTER_WELLCOMING_NEWSLETTER_SENT_SUCCESSFULLY', 
							LogHelper::CAT_SUBSCRIPTION, 
							array('Email' => $subscriber->email, 'Newsletter' => $newsletter->name));
						
					} else {
						throw new Exception(json_encode($res->errors));
					}

				} catch(Exception $e) {
					LogHelper::addError(
						'COM_NEWSLETTER_WELCOMING_SEND_FAILED', 
						LogHelper::CAT_SUBSCRIPTION, 
						array(
							'Error' => $e->getMessage(),
							'Email' => $subscriber->email,
							'Newsletter' => $newsletter->name));
				}	
			}
		}	
		
		jexit($message);
	}



	/**
	 * The method to cunfirm the subscription.
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

		// Check token, d_i_e on error.
		//JRequest::checkToken() or jexit('Invalid Token');

		if (empty($uid) || empty($nid)) {
			
			// Log about trouble
			LogHelper::addError(
				'COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_PARAMETERS_NOT_FOUND',
				LogHelper::CAT_SUBSCRIPTION,
				array(
					'Newsletter id' => $nid,
					'Subscriber\'s key' => $uid));

			jexit(JText::_('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_PARAMETERS_NOT_FOUND'));
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

		try {
			// If we parameters are not enough...
			if (empty($uid) || empty($lists)) {
				throw new Exception ('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_PARAMETERS_NOT_FOUND');
			}

			// Insert into db
			// TODO: Add santiy checks, use model instead
			$db->setQuery( "SELECT * FROM #__newsletter_subscribers WHERE subscription_key = " . $db->quote(addslashes($uid)) );
			$subscriber = $db->loadObject();
			if (empty($subscriber->subscriber_id)) {
				throw new Exception('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_SUBSCRIBER_NOT_FOUND');
			}

			// Check the newsletter if nid is present
			if (!empty($nid)) {
				$db->setQuery( "SELECT newsletter_id FROM #__newsletter_newsletters WHERE newsletter_id = " . $db->quote(addslashes($nid)));
				$newsletter = $db->loadObject();
				if (empty($newsletter->newsletter_id)) {
					throw new Exception('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_NEWSLETTER_NOT_FOUND');
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
			$amManager->processUnsubscription(array(
				'subscriberId' => (int)$subscriber->subscriber_id));

			$app->triggerEvent(
				'onMigurNewsletterAfterUnsubscribe', 
				array(
					'subscriber' => $subscriber,
					'lists' => $lists,
					'result' => $res
			));
			
		} catch (Exception $e){
			
			// Log about this incedent
			$msg = $e->getMessage();
			
			LogHelper::addError(
				$msg,
				LogHelper::CAT_SUBSCRIPTION,
				array(
					'Newsletter id' => $nid,
					'Subscriber id' => $uid,
					'Lists ids' => $lists));

			$this->setRedirect(
				JRoute::_('index.php?option=com_newsletter&view=subscribe&layout=unsubscribe&uid='.$uid.'&nid='.$nid, false),
				JText::_($msg),
				'error');
			
			return;
		}	


		// Logging for debug
		LogHelper::addDebug(
			'Unsubscription complete.',
			LogHelper::CAT_SUBSCRIPTION,
			array(
				'Newsletter id' => $nid,
				'Subscriber id' => $uid,
				'Lists ids' => $lists));
		
		
		// Redirect to page
		$this->setRedirect(
			JRoute::_('index.php?option=com_newsletter&view=subscribe&layout=unsubscribe&uid='.$uid.'&nid='.$nid, false),
			JText::sprintf('COM_NEWSLETTER_THANK_YOU_FOR_USING_SERVICE', $subscriber->name),
			'message');
	}
}

