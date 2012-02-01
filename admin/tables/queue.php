<?php

/**
 * The queue table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of queue table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableQueue extends JTable
{

	const STATE_SENT = 0;
	const STATE_INQUEUE = 1;
	const STATE_BOUNCED = 2;
	
	
	/**
	 * The constructor of a class.
	 *
	 * @param	object	$config		An object of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$_db)
	{
		parent::__construct(
				'#__newsletter_queue',
				'queue_id',
				$_db
		);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 * 					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
	 * @param	integer The user id of the user performing the operation.
	 * 
	 * @return	boolean	True on success.
	 * @since	1.0
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('List', 'NewsletterTable');

		// For all keys
		foreach ($pks as $pk) {
			// Load the banner
			if (!$table->load($pk)) {
				$this->setError($table->getError());
			}

			// Change the state
			$table->state = $state;

			// Store the row
			if (!$table->store()) {
				$this->setError($table->getError());
			}
		}
		return count($this->getErrors()) == 0;
	}

	
	public function setBounced($sid, $nid)
	{
		if (empty($sid)) {
			return false;
		}

		$this->load(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid
		));

		if (empty($this->queue_id)) {
			return false;
		}

		return $this->save(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid,
			'state' => NewsletterTableQueue::STATE_BOUNCED,
			'date' => date('Y-m-d H:i:s')
		));
	}	
}

