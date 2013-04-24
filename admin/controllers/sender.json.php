<?php

/**
 * The controller for sender view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerSender extends JControllerForm
{

	/**
	 * Class Constructor
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
	 * Handles the sending of the letter to a lists. 
	 * Adds the items to "_queue" table.
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function addToQueue()
	{
		$newsletterId = JRequest::getInt('newsletter_id');
		$lists = JRequest::getVar('lists');

		$limit = JRequest::getInt('limit', 1000);
		$offset = JRequest::getVar('offset', '');
		
		if (empty($newsletterId) || empty($lists)) {
			$this->setError('Required data is absent');
			NewsletterHelper::jsonError(JText::_('Required data is absent'));
		}

		$requestId = md5(
			JRequest::getString('newsletter_id').':'.
			JRequest::getString('lists')
		);
		
		$statePath = 'com_newsletter.'.md5('addtoqueue.' . $requestId);
		
		$app = JFactory::getApplication();
		
		// If there is no extarnal offset thern use internal from session
		if (!is_numeric($offset)) {
			$offset = $app->getUserState($statePath . '.offset', 0);
		}	

		// If this is a start then init session variables
		if ($offset == 0) {
			$app->setUserState($statePath.'.errors', 0);
		}
		
		// Restore previous state
		$errors = $app->getUserState($statePath.'.errors', 0);
		
		// Fetch subscribers
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query
			->select('distinct s.subscriber_id, sl.list_id')
			->from('#__newsletter_sub_list AS sl')
			->join('', '#__newsletter_subscribers AS s ON s.subscriber_id = sl.subscriber_id')
			->where('list_id IN (' . implode(',', $lists) . ')');

		$dbo->setQuery($query, $offset, $limit);
		$subs = $dbo->loadAssocList();

		$fetched = count($subs);
		$processed = 0;
		
		if (!empty($subs)) {

			$table = JTable::getInstance('queue', 'NewsletterTable');

			// Let's Speeeeeed up this script!
			$transactionItemsCount = 0;
			$dbo->setQuery('SET AUTOCOMMIT=0;');
			$dbo->query();

			foreach ($subs as $item) {

				$table->reset();
				$table->queue_id = null;

				if (!$table->load(array(
						'newsletter_id' => $newsletterId,
						'subscriber_id' => $item['subscriber_id'],
						'list_id'       => $item['list_id']))
				) {

					// add new row only if it does not exist...
					if ($table->save(array(
						'newsletter_id' => $newsletterId,
						'subscriber_id' => $item['subscriber_id'],
						'list_id'       => $item['list_id'],
						'created' => date('Y-m-d H:i:s'),
						'state' => 1))) {

					} else {

						$errors++;
					}
				}

				// Handle the transaction
				// Commit each 100 items
				$transactionItemsCount++;

				if ($transactionItemsCount > 500) {
					$dbo->setQuery('COMMIT;');
					$dbo->query();
					$transactionItemsCount = 0;
				}

				$processed++;
			}

			// Commit it all!
			$dbo->setQuery('COMMIT;');
			$dbo->query();

			$dbo->setQuery('SET AUTOCOMMIT=0;');
			$dbo->query();
		}

		// Store offsets and stats
		$app->setUserState($statePath.'.offset', $offset + $fetched);
		$app->setUserState($statePath.'.errors', $errors);

		if ($processed == 0) {
			$app->setUserState($statePath.'.offset', 0);
			$app->setUserState($statePath.'.errors', 0);
		}

		$data = array(
			'total'    => $offset + $fetched,
			'fetched'  => $fetched,
			'errors'   => $errors
		);
		
		if ($errors > 0) {
			NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_AN_ERROR_OCCURED'), $data);
		}
		
		NewsletterHelper::jsonMessage('ok', $data);
	}
}

