<?php

/**
 * The cron controller file.
 *
 * @version       $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('migur.library.mailer');

/*
 * Class of the Subscribe controller.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */

class NewsletterControllerSubscribe extends MigurController
{
	/**
	 * The constructor of a class
	 *
	 * @param    array    $config        An optional associative array of configuration settings.
	 *
	 * @return    void
	 * @since    1.0
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
		$html = (int)JRequest::getInt('newsletter-html', null);
		$listsIds = NewsletterHelperData::toArrayOfInts(JRequest::getVar('newsletter-lists', array()));
		$fbenabled = JRequest::getInt('fbenabled', array());
		//$sendto = JRequest::getVar('sendto');
		// Check token, d_i_e on error.
		//JRequest::checkToken() or jexit('Invalid Token');

		if (empty($name) || empty($email) || !in_array($html, array(0, 1)) || empty($listsIds)) {

			$msg = JText::_('COM_NEWSLETTER_PARAMETERS_NOT_FOUND');

			NewsletterHelperLog::addDebug(
				$msg, NewsletterHelperLog::CAT_SUBSCRIPTION, array(
					'name' => $name,
					'email' => $email,
					'is html' => $html,
					'list ids' => $listsIds)
			);

			jexit($msg);
		}

		$comParams = JComponentHelper::getComponent('com_newsletter')->params;

		$trusted = false;

		// try to get user data from FB
		$fbAppId = $comParams->get('fbappid');
		$fbSecret = $comParams->get('fbsecret');
		if (!empty($fbAppId) && !empty($fbSecret) && !empty($fbenabled)) {
			$me = NewsletterHelperSubscriber::getFbMe($fbAppId, $fbSecret);
			if (!empty($me['email']) && $me['email'] == $email) {
				$trusted = true;
			}
		}

		// If this is a current user's email
		// then this user is trusted
		if (!empty($user->id) && $user->email == $email) {
			$trusted = true;
		}


		// Trying to find subscriber or J!user with provided email.
		// TODO Need to replace this 'NewsletterModelEntity' to 'NewsletterModel'
		$subscriber = MigurModel::getInstance('Subscriber', 'NewsletterModelEntity');
		$subscriber->load(array('email' => $email));

		// If not found then this email does not belongs anyone in system.
		// So we can create the Migur subscriber if registration is alowed.
		if (!$subscriber->getId()) {

			// Check if the registration is disabled then
			// there is nothing to do.
			if (NewsletterHelperNewsletter::getParam('general_reg_disable') == '1') {
				jexit(JText::_('COM_NEWSLETTER_REGISTRATION_IS_DISABLED'));
			}

			// If enabled then create MIGUR subscriber and continue
			$subscriber->create(array(
				'name' => $name,
				'email' => $email,
				'state' => '1',
				'html' => $html));
		}

		// Confirm subscriber if provided email is the email of current J!user
		// or email is email of user that currently logged in FACEBOOK.
		// Confirm all its assignings to lists as well
		if ($trusted) {
			$subscriber->confirm();
		}

		$message = JText::sprintf('COM_NEWSLETTER_THANK_YOU_FOR_SUBSCRIBING', $name);

		$listModel = MigurModel::getInstance('List',  'NewsletterModel');

		$assignedListsIds = array();

		$sid = $subscriber->getId();

		// Process each list
		foreach ($listsIds as $lid) {

			$list = $listModel->getItem($lid);
			$hasSubscriber = $listModel->hasSubscriber($lid, $sid);

			if (!$hasSubscriber) {

				$listModel->assignSubscriber(
					$lid, $subscriber->toArray(),
					array('confirmed' => ($list->autoconfirm || $trusted))
				);

				// Add to history all subscriptions
				$history = JTable::getInstance('history', 'NewsletterTable');
				$history->save(array(
					'subscriber_id' => $sid,
					'list_id' => $list->list_id,
					'newsletter_id' => NULL,
					'action' => NewsletterTableHistory::ACTION_SIGNEDUP,
					'date' => date('Y-m-d H:i:s'),
					'text' => addslashes($list->name)
				));
				unset($history);

				$assignedListsIds[] = $lid;

			} else {

				if (($list->autoconfirm || $trusted)) {
					$listModel->confirmSubscriber($lid, $sid);

					$message =
						JText::sprintf('COM_NEWSLETTER_YOU_HAVE_SUBSCRIBED_TO', $name) . ' ' .
							JText::_('COM_NEWSLETTER_LIST_ALREADY');
					jexit($message);
				}
			}

			// If list is not confirmed then send the newsletter
			if (!$listModel->isConfirmed($lid, $sid)) {

				// Immediately mail subscription letter
				$res = $listModel->sendSubscriptionMail(
					$subscriber,
					$list->list_id,
					array(
						'ignoreDuplicates' => true
					));

				// Set message and add some logs
				if ($res) {
					if ($hasSubscriber) {
						$message = JText::_('COM_NEWSLETTER_YOU_ADDED_TO_LIST_ALREADY');
					} else {
						$message =
							JText::sprintf('COM_NEWSLETTER_THANK_YOU_FOR_SUBSCRIBING', $name) . ' ' .
								JText::_('COM_NEWSLETTER_YOU_WILL_NEED_CONFIRM_SUBSCRIPTION');
					}
				}
			}
		}

		// Triggering the subscribed plugins.
		// Process automailing via internal plugin plgMigurAutomail
		JFactory::getApplication()->triggerEvent(
			'onMigurAfterSubscribe',
			array(
				'subscriberId' => $sid,
				'lists' => $assignedListsIds)
		);

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
		$lid = JRequest::getString('lid', null);

		if (empty($subKey)) {
			// Redirect to page
			$message = JText::_("COM_NEWSLETTER_AN_ERROR_OCCURED");
			$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid=' . $subKey, $message, 'error');
			return;
		}
		$db->setQuery("SELECT subscriber_id FROM #__newsletter_subscribers WHERE subscription_key=" . $db->quote($subKey));
		$subscriber = $db->loadObject();
		if (count($subscriber) < 1) {
			// Redirect to page
			$message = JText::_("COM_NEWSLETTER_AN_ERROR_OCCURED");
			$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid=' . $subKey, $message, 'error');
			return;
		}

		// Check if the registration is disabled then
		// there is nothing to do.
		if (NewsletterHelperNewsletter::getParam('general_reg_disable') == '1') {
			$message = JText::_('COM_NEWSLETTER_REGISTRATION_IS_DISABLED');
			$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid=' . $subKey, $message, 'error');
			return true;
		}

		// Insert into db
		// TODO: Add santiy checks, use model instead
		$db->setQuery("UPDATE #__newsletter_subscribers set confirmed=1 WHERE confirmed=" . $db->quote($subKey));
		$subscriber = $db->query();

		if (!empty($lid)) {
			$db->setQuery(
				"UPDATE #__newsletter_sub_list set confirmed=1 WHERE " .
					" confirmed=" . $db->quote($subKey) .
					" AND list_id=" . $db->quote($lid));
			$subscriber = $db->query();
		}

		// Redirect to page
		$message = JText::_("COM_NEWSLETTER_YOUR_SUBSCRIPTION_CONFIRMED");
		$this->setRedirect('?option=com_newsletter&view=subscribe&layout=confirmed&uid=' . $subKey, $message, 'message');

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
			NewsletterHelperLog::addError(
				'COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_PARAMETERS_NOT_FOUND', NewsletterHelperLog::CAT_SUBSCRIPTION, array(
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
		$uid = JRequest::getString('newsletter-uid', '');
		$nid = JRequest::getString('newsletter-nid', '');
		$lists = JRequest::getVar('newsletter-lists', array());

		try {
			// If we parameters are not enough...
			if (empty($uid) || empty($lists)) {
				throw new Exception('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_PARAMETERS_NOT_FOUND');
			}

			// Insert into db
			// TODO: Add santiy checks, use model instead
			$db->setQuery("SELECT * FROM #__newsletter_subscribers WHERE subscription_key = " . $db->quote(addslashes($uid)));
			$subscriber = $db->loadObject();
			if (empty($subscriber->subscriber_id)) {
				throw new Exception('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_SUBSCRIBER_NOT_FOUND');
			}

			// Check the newsletter if nid is present
			if (!empty($nid)) {
				$db->setQuery("SELECT newsletter_id FROM #__newsletter_newsletters WHERE newsletter_id = " . $db->quote(addslashes($nid)));
				$newsletter = $db->loadObject();
				if (empty($newsletter->newsletter_id)) {
					throw new Exception('COM_NEWSLETTER_UNSUBSCRIPTION_FAILED_NEWSLETTER_NOT_FOUND');
				}
			}

			$app->triggerEvent(
				'onMigurBeforeUnsubscribe', array(
				'subscriber' => $subscriber,
				'lists' => $lists
			));

			// Legacy event
			$app->triggerEvent(
				'onMigurNewsletterBeforeUnsubscribe', array(
				'subscriber' => $subscriber,
				'lists' => $lists
			));

			foreach ($lists as $list) {

				// Delete subscriptions from list
				$db->setQuery(
					"DELETE FROM #__newsletter_sub_list " .
						"WHERE subscriber_id = " . $db->quote((int)$subscriber->subscriber_id) . " AND list_id = " . $db->quote((int)$list)
				);
				$db->query();

				// Add to history
				$newsletterId = !empty($nid) ? $db->quote((int)$nid) : "NULL";

				$db->setQuery(
					"INSERT IGNORE INTO #__newsletter_sub_history SET " .
						" newsletter_id = " . $newsletterId . ", " .
						" subscriber_id=" . $db->quote((int)$subscriber->subscriber_id) . ", " .
						" list_id=" . $db->quote((int)$list) . ", " .
						" date=" . $db->quote(date('Y-m-d H:i:s')) . ", " .
						" action=" . $db->quote(NewsletterTableHistory::ACTION_UNSUBSCRIBED) . ", " .
						" text=''"
				);
				$res = $db->query();
			}


			// Triggering plugins.
			// Process automailing via internal plugin plgMigurAutomail
			JFactory::getApplication()->triggerEvent('onMigurUnsubscribe', array(
				'subscriberId' => (int)$subscriber->subscriber_id,
				'lists' => $lists));

			$app->triggerEvent(
				'onMigurAfterUnsubscribe', array(
				'subscriberId' => $subscriber->subscriber_id,
				'lists' => $lists,
				'result' => $res
			));
		} catch (Exception $e) {

			// Log about this incedent
			$msg = $e->getMessage();

			NewsletterHelperLog::addError(
				$msg, NewsletterHelperLog::CAT_SUBSCRIPTION, array(
				'Newsletter id' => $nid,
				'Subscriber id' => $uid,
				'Lists ids' => $lists));

			$this->setRedirect(
				JRoute::_('index.php?option=com_newsletter&view=subscribe&layout=unsubscribe&uid=' . $uid . '&nid=' . $nid, false), JText::_($msg), 'error');

			return;
		}


		// Logging for debug
		NewsletterHelperLog::addDebug(
			'Unsubscription complete.', NewsletterHelperLog::CAT_SUBSCRIPTION, array(
			'Newsletter id' => $nid,
			'Subscriber id' => $uid,
			'Lists ids' => $lists));


		// Redirect to page
		$this->setRedirect(
			JRoute::_('index.php?option=com_newsletter&view=subscribe&layout=unsubscribe&uid=' . $uid . '&nid=' . $nid, false), JText::sprintf('COM_NEWSLETTER_THANK_YOU_FOR_USING_SERVICE', $subscriber->name), 'message');
	}

}

