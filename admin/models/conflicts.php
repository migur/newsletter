<?php

/**
 * The conflicts list model. Implements the standard functional for conflicts list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of conflicts list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelConflicts extends MigurModelList
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
				'subName', 'subEmail', 'userName', 'userEmail'
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
		parent::populateState('subName', 'asc');
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

		$query->select(
			's.subscriber_id AS subSubId, s.name AS subName, s.email AS subEmail, '.
			'u.id AS userId, u.name AS userName, u.email AS userEmail');
		$query->from('#__newsletter_subscribers AS s');
		$query->join('', '#__users AS u ON s.email = u.email AND s.user_id != u.id');

		
		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}
		unset($this->filtering);

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
			$orderCol = 'subName';
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$this->query = $query;
	}

	
}
