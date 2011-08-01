<?php

/**
 * The "subscribers of list" model. Implements the standard functionality for subscribers binded to list.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

/**
 * Class of "subscribers of a list" model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSublists extends MigurModelList
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

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'sublist_id',
				'subscriber_id',
				'list_id',
				'subscriber_name',
				'list_name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Sets the default query
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDefaultQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.sublist_id, a.subscriber_id, s.name as subscriber_name, a.list_id, l.name as list_name'
			)
		);

		$query->from('#__newsletter_sub_list AS a');
		$query->join('LEFT', '#__newsletter_subscribers AS s ON a.subscriber_id=s.subscriber_id');
		$query->join('LEFT', '#__newsletter_lists AS l ON a.list_id=l.list_id');

		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'subscriber_name');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		// echo nl2br(str_replace('#__','jos_',$query));
		$this->query = $query;
	}

	/**
	 * Assign subscriber ($userId) to list ($listId).
	 *
	 * @param int $userId user id
	 * @param int $listId list id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function asignUserTo($userId, $listId)
	{
		$table = $this->getTable();

		$data = array(
			'user_id' => $userId,
			'list_id' => $listId
		);

		if (!$table->load($data)) {
			$table->reset();
			$table->set($table->getKeyName(), null);
			$table->bind($data);
			return $table->store();
		}

		return true;
	}

}
