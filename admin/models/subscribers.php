<?php

/**
 * The subscribers list model. Implements the standard functional for subscribers list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSubscribers extends MigurModelList
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
				'title', 'a.title',
				'email', 'a.email',
				'state', 'a.state',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'list', 'sl.list_id',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param string $ordering - name of column
	 * @param string $direction - direction
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.' . $layout;
		}

		$form = JRequest::getVar('form');
		$name = $this->getName();
		if ($form != $name) {
			$list = $app->getUserState($this->context . '.filter.list');
			$type = $app->getUserState($this->context . '.filter.type');
			$published = $app->getUserState($this->context . '.filter.published');
			$published = ($published) ? $published : '';
			$search = $app->getUserState($this->context . '.filter.search');
		} else {
			$list = $this->getUserStateFromRequest($this->context . '.filter.list', 'filter_list', '');
			$type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '');
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			if ($search == "Search...") {
				$search = "";
			}
		}

		$this->setState('filter.type', $type);
		$this->setState('filter.list', $list);
		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.name', 'asc');
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
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function setDefaultQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		
//		10000 users 4500 subscribers - result 606ms (filesort)
//		10000 users 4500 subscribers - result 351ms (indexes)
//		
//		14500 subscribers - result 353ms (filesort)
//		14500 subscribers - result 16ms (indexes)
		
		
		// Select the required fields from the table.
//		$query->select('a.subscriber_id AS id, a.name, a.email, a.state, a.created_on' .
//				', a.created_by, a.modified_on, a.modified_by, a.locked_on, a.locked_by');
//
//		$query->from('#__newsletter_subscribers AS a');
		

//SELECT COUNT(*) 
//FROM #__users AS u
//LEFT JOIN #__newsletter_subscribers AS s ON s.user_id = u.id
//WHERE s.subscriber_id IS NULL;
//
//SELECT COUNT(*)
//FROM #__users AS u
//WHERE u.id NOT IN (SELECT s.user_id FROM #__newsletter_subscribers AS s);		
		
		
		$query->select('*');
		
//		$query->from(
//		'(SELECT s.subscriber_id, s.name, s.email, s.state, s.html, s.user_id
//		FROM #__newsletter_subscribers AS s
//		WHERE s.user_id = 0 OR s.user_id IS NULL
//
//		UNION 
//
//		SELECT s.subscriber_id, u.name, u.email, u.block, s.html, s.user_id
//		FROM #__newsletter_subscribers AS s
//		JOIN #__users AS u ON (s.user_id > 0 AND u.id = s.user_id)
//
//		UNION 
//
//		SELECT NULL AS subscriber_id, u.name, u.email, u.block, 1 AS html, u.id
//		FROM #__newsletter_subscribers AS s
//		RIGHT JOIN #__users AS u ON (u.id = s.user_id AND s.subscriber_id IS NULL)) AS a');


		
// May halp to optimize it!!!!!!!!!!!!!!!!!!!!!11111
//		CREATE TEMPORARY TABLE temp_union TYPE=HEAP *cool_select_statement_1*;
//		INSERT INTO temp_union *cool_select_statement_2*;
//		SELECT * FROM temp_union *order_and_group_by_stuff*;
//		DROP TABLE temp_union;
		
		$query->from(
			'(SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			LEFT JOIN #__users AS u ON (s.user_id = u.id)

			UNION

			SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			RIGHT JOIN #__users AS u ON (s.user_id = u.id)) AS a');
		
		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}
		unset($this->filtering);

		// Filter by list state
		$list = $this->getState('filter.list');
		if (!empty($list)) {
			$query->leftJoin("#__newsletter_sub_list AS sl ON a.subscriber_id=sl.subscriber_id");
			$query->where('sl.list_id = ' . (int) $list);
		}

		// Filter by list state
		$type = $this->getState('filter.type');
		
		if ($type == 1) {
			$query->where('a.user_id IS NULL');
		}
		
		if ($type == 2) {
			$query->where('a.user_id > 0');
		}
		
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (in_array($published, array('0', '1'))) {
			$query->where('a.state = ' . (int) $published);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else if (stripos($search, 'name:') === 0) {
				$search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
				$query->where('(a.name LIKE ' . $search . ')');
			} else {
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.email LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol == 'a.ordering' || $orderCol == 'a.name') {
			$orderCol = 'name';
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$this->query = $query;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getSubscribersByList($params)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'DISTINCT a.subscriber_id AS id, a.name, a.email');

		$query->from(
			'(SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			LEFT JOIN #__users AS u ON (s.user_id = u.id)

			UNION

			SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			RIGHT JOIN #__users AS u ON (s.user_id = u.id)) AS a');
		
		$query->join('', "#__newsletter_sub_list AS sl ON a.subscriber_id=sl.subscriber_id");

		if (!empty($params['list_id'])) {
			
			$query->where('sl.list_id=' . intval($params['list_id']));
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol == 'a.ordering' || $orderCol == 'a.name') {
			$orderCol = 'name ' . $orderDirn . ', a.subscriber_id';
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getUnsubscribedList($params)
	{
		if (!$this->state->get('list.unsubscribed.ordering')) {
			$orderCol = $this->state->set('list.unsubscribed.ordering', 'date');
		}

		if (!$this->state->get('list.unsubscribed.direction')) {
			$orderCol = $this->state->set('list.unsubscribed.direction', 'desc');
		}

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select('a.*, h.date, h.text');

		$query->from('#__newsletter_subscribers AS a');
		$query->join('', "#__newsletter_sub_history AS h ON a.subscriber_id=h.subscriber_id AND h.list_id='" . intval($params['list_id']) . "'");
		$query->where("action='" . intval(NewsletterTableHistory::ACTION_UNSUBSCRIBED) . "'");

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.unsubscribed.ordering');
		$orderDirn = $this->state->get('list.unsubscribed.direction');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); //die();
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

}
