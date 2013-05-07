<?php

/**
 * The Manager for acyMailer Component.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

class NewsletterModelImportCommon
{

	static $managers = array();
	
	/**
	 * Fetch the subscribers from acyMailer component to array
	 *
	 * @return Array
	 * @since  1.0
	 */
	public function exportSubscribers()
	{
		return array();
	}

	/**
	 * Fetch the lists from acyMailer component to array
	 *
	 * @return array - array of objects
	 * @since  1.0
	 */
	public function exportLists($offset = 0, $limit = 0)
	{
		return array();
	}

	/**
	 * Imports the subscribers into acyMailer component
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function importSubscribers(array $list)
	{
		return true;
	}

	/**
	 * Imports the data about subscribers and lists into com_newsletter
	 *
	 * @param  array - the array of the objects(subscriber - list)
	 *
	 * @return mixed - integer/(bool)false on success/fail 
	 * @since  1.0
	 */
	public function importLists($list)
	{
		$lists = array();
		$subs = array();

		$db = JFactory::getDbo();

		$added = 0;
		$assigned = 0;
		$errors = 0;
		
		$subManager = MigurModel::getInstance('Subscriber', 'NewsletterModel');
		$subTable = $subManager->getTable();
		
		$listManager = MigurModel::getInstance('List', 'NewsletterModel');
		$listTable = JTable::getInstance('List', 'NewsletterTable');
		
		$isTransaction = false;
		$transactionItemsCount = 0;
		
		foreach ($list as $item) {

			// Let's Speeeeeed up this script in at least 50 times!
			if (!$isTransaction) {
				$db->transactionStart();
				$isTransaction = true;
			}
			
			NewsletterHelper::setTimeLimit(30);
			
			$lists[$item['list_name']] = 0;
			$subs[$item['email']] = $item;

			// Add new subscribers with not existing emails
			if (!empty($item['email'])) {
				
				// Spasm of memory economy :)
				// Need to use model because there is functionality to load juser-subscriber
				$subscriber = $subManager->getItem(array('email' => $item['email']), $subTable);
				
				if ($subscriber !== false) {
					
					$sid = $subscriber['subscriber_id'];
					
				} else {

					$saveData = array(
						'name' => stripslashes($item['name']),
						'email' => stripslashes($item['email']),
						'html' => (int) DataHelper::getDefault('html', 'subscriber'),
						'state' => (int) DataHelper::getDefault('state', 'subscriber'),
						'created_on' => stripslashes($item['created']),
					);
					
					// Spasm of memory economy. Again. :)
					if ($subManager->save($saveData, 'fastStore', $subTable)) {
						$sid = $subTable->subscriber_id;
						$added++;
					} else {
						$errors++;
					}	
				}
			}


			// Create non-exist list.
			if (!empty($item['list_name'])) {
				
				if(!$listTable->load(array('name' => $item['list_name']))) {
					$listTable->save(array(
						'name' => $item['list_name'],
						'created_on' => date('Y-m-d H:i:s')
					));
				}
				
				$lid = $listTable->list_id;
			}

			// Assign user to list if needed and do all we should.
			if (
				!empty($sid) && 
				!empty($lid) && 
				!$listManager->hasSubscriber($lid, $sid)
			) {
				
				if (empty($subscriber)) {
					$subscriber = $subManager->getItem($sid, $subTable);
				}

				try {
					// Send message
//					$listManager->sendSubscriptionMail(
//						$subscriber,
//						$lid, 
//						array(
//							'addToQueue'       => true,
//							'ignoreDuplicates' => true
//					));

					// Assign to list
					$listManager->assignSubscriber($lid, $subscriber);

					// Triggering the subscribed plugins.
					// Process automailing via internal plugin plgMigurAutomail
					JFactory::getApplication()->triggerEvent(
						'onMigurAfterSubscribe', 
						array(
							'subscriberId' => $subscriber['subscriber_id'],
							'lists' => array($lid))
					);
					
					$assigned++;
					
				} catch(Exception $e) {
					
					LogHelper::addError(
						'COM_NEWSLETTER_ASSIGN_FAILED', LogHelper::CAT_SUBSCRIPTION, array(
						'Error' => $e->getMessage(),
						'Email' => $subscriber['email'],
						'List' => $newsletter->name
					));
					
					$errors++;
				}
			}
			
			// Handle the transaction
			// Commit each 100 items
			$transactionItemsCount++;

			if ($transactionItemsCount > 500 && $isTransaction) {
				$db->transactionCommit();
				$transactionItemsCount = 0;
				$isTransaction = false;
			}
		}

		// Commit it all!
		if ($isTransaction) {
			$db->transactionCommit();
		}

		return array(
			'added'    => $added,
			'assigned' => $assigned,
			'errors'   => $errors
		);
			
	}

	/**
	 * Check the structure of a exported/imported tables
	 *
	 * @param array - the list to import
	 *
	 * @return bool
	 * @since  1.0
	 */
	public function isValid()
	{
		// TODO deprecated since 12.1 Use PHP Exception
		JError::throwError('isValid should be implemented (' . $this->getName() . ')');
	}

	public function validateTable($name, $needed)
	{
		$db = JFactory::getDbo();
		
		try {
			
			$fields = @$db->getTableColumns($name);
			
		} catch (Exception $e) {
			return false;
		}

		if (empty($fields)) {
			return false;
		}

		$fields = array_keys($fields);
		
		foreach ($needed as $item) {
			// Check the needed fields
			if (!in_array($item, $fields)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the name.
	 * 
	 * @return string
	 * @since  1.0
	 */
	public function getName()
	{
		return $this->name;
	}

	
	/**
	 * Get the component manager instance
	 *
	 * @param  string - the type of a component
	 *
	 * @return object  - an instance of a mananger
	 * @since  1.0
	 */
	public static function getInstance($com)
	{
		if (!empty(self::$managers[$com]) && is_object(self::$managers[$com])) {
			return self::$managers[$com];
		}
		if (!@include_once strtolower($com) . '.php') {
			return false;
		}

		$class = 'NewsletterModelImport' . ucfirst($com);
		
		$man = new $class;
		
		self::$managers[$com] = $man;
		return self::$managers[$com];
	}

	
	/**
	 * Get all supported components and check if they are valid to import
	 *
	 * @return array - array of objects (info about component)
	 */
	public static function getSupported()
	{
		// Get a list of files
		$files = glob(dirname(__FILE__) . '/*.php');
		
		// Fetch all supported component managers
		$res = array();
		foreach ($files as $com) {

			$path = explode(DIRECTORY_SEPARATOR, $com);
			
			if (count($path) == 0) {
				continue;
			}
			
			$com = $path[count($path)-1];
			if ($com == 'common.php') {
				continue;
			}

			$com = str_replace('.php', '', $com);
			
			$item = new stdClass();
			$item->type = $com;
			$item->valid = false;
			$item->name = null;

			$man = self::getInstance($com);
			if (is_object($man)) {
				$item->valid = $man->isValid();
				$item->name = $man->getName();
			}
			$res[] = $item;
		}
		
		return $res;
	}
}
