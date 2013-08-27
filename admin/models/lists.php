<?php

/**
 * The lists model. Implements the standard functional for lists view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('models.entities.list', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of the lists model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelLists extends MigurModelList
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
				'id', 'a.id',
				'name', 'a.name',
				'enabled', 'a.enabled',
				'state', 'a.state',
				'created', 'a.created',
				'subscribers', 'a.subscribers',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering'
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');

		return parent::getStoreId($id);
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	protected function setDefaultQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*, sl.subscribers'));

		// Add data from subscribers table if it's need.
		$query->from('#__newsletter_lists AS a');
		$query->join(
			'LEFT',
			'(SELECT list_id, count(*) as subscribers ' .
			' FROM #__newsletter_sub_list AS t1, ' .
			' #__newsletter_subscribers AS t2 ' .
			' WHERE t1.subscriber_id = t2.subscriber_id ' .
			' GROUP BY list_id) as sl ON a.list_id=sl.list_id'
		);

		// Filter by published state
		$published = $this->getState('filter.published');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (in_array($published, array('0', '1', '-2'))) {
			$query->where('a.state = ' . (int) $published);
		} elseif($published != '*') {
			$query->where('a.state >= 0');
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else if (stripos($search, 'name:') === 0) {
				$search = $db->Quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(a.name LIKE ' . $search . ')');
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ')');
			}
		}

		$ffields = $this->getState('filter.fields');
		if (is_array($ffields)) {
			foreach ($ffields as $condition) {
				$query->where($condition);
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		$this->query = $query;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function setSubscriberQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$fields = 'a.list_id, a.name, a.state, a.ordering, a.created_on' .
			', a.created_by, a.modified_on, a.modified_by' .
			', a.locked_on, a.locked_by';

		// Add data from subscribers table if it's need.
		if (!empty($this->filtering['subscriber_id'])) {
			$query->join(
				'LEFT',
				'#__newsletter_sub_list AS sl ON a.list_id=sl.list_id AND sl.subscriber_id=' . intval($this->filtering['subscriber_id'])
			);
			$fields .= ', sl.subscriber_id';
		}

		$query->select($this->getState('list.select', $fields));
		$query->from('#__newsletter_lists AS a');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else if (stripos($search, 'name:') === 0) {
				$search = $db->Quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(a.name LIKE ' . $search . ')');
			} else {
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol == 'a.ordering' || $orderCol == 'a.name') {
			$orderCol = 'name ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		$this->query = $query;
	}


	/**
	 * Gets a list of all active lists
	 * without pagination and other limitations
	 *
	 * @return array of objects
	 */
	public function getAllActive($idonly = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			  ->from('#__newsletter_lists')
			  ->where('state=1');

		$db->setQuery($query);

		if (!empty($idonly)) {
			return $db->loadObjectList(null, 'list_id');
		}

		return $db->loadObjectList();
	}
}
