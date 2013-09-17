<?php

/**
 * The extended version of JModelList.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.application.component.modellist');

/**
 * Class extends the functionality of JModelList
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurModelList extends JModelList
{
	/*
	 *  The name of function to retrive the list of items.
	 *  Allow to use not only "getListQuery()".
	 */
	const STATE_TRASHED = -2;

	protected $_queryType = null;

	public $tableClassName = null;

	public $tableClassPrefix = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param	string	An optional ordering field.
	 * @param	string	An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since	1.0
	 */
	protected function populateState($ordering = null, $direction = null, $options = array())
	{
		$app = JFactory::getApplication();
		$form = $app->input->post->get('form');

		// If the context is set, assume that stateful lists are used.
		if ($this->context) {

			if (empty($options['fields'])) $options['fields'] = array();
			$additionalFields = array();

			/**
			 * If 'form' variable is mismatched with model name
			 * then set data from user state
			 * because this post data is addressed to another model
			 */
			$name = $this->getName();
			if (empty($form) || $form == $name) {

				parent::populateState($ordering = null, $direction = null);

				// Override global limit with models own one.
				$limit = $app->getUserState('global.list.limit', $app->getCfg('list_limit'));
				$limit = $this->getUserStateFromRequest($this->context . '.limit', 'limit', $limit);
				$this->setState('list.limit', $limit);


				$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
				$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');

				foreach($options['fields'] as $key => $value) {
					$additionalFields[$key] = $this->getUserStateFromRequest($this->context . '.' . $key, $value[0], $value[1]);
				}

			} else {

				// Defaults
				$limit = $app->getUserState('global.list.limit', $app->getCfg('list_limit'));

				// Get data from user state
				// global for all lists(models)
				$limit = $app->getUserState($this->context . '.limit', $limit);
				$start = $app->getUserState($this->context . '.limitstart', 0);
				$order = $app->getUserState($this->context . '.ordercol');
				$dir   = $app->getUserState($this->context . '.orderdirn', 'ASC');

				$search    = $app->getUserState($this->context . '.filter.search', null);
				$published = $app->getUserState($this->context . '.filter.published', null);

				//// Update user state and model state
				$this->setState('list.start', $limit > 0? (floor($start / $limit) * $limit) : 0);
				$this->setState('list.ordering', $order);
				$this->setState('list.direction', $dir);
				$this->setState('list.limit', $limit);

				foreach($options['fields'] as $key => $value) {
					$additionalFields[$key] = $app->getUserState($this->context . '.' . $key, $value[1]);
				}
			}

			// Add functionality to populate SEARCH and PUBLISH state
			$this->setState('filter.search', $search);
			$this->setState('filter.published', $published);

			foreach($additionalFields as $key => $value) {
				$this->setState($key, $value);
			}

		} else {
			$this->setState('list.start', 0);
			$this->state->set('list.limit', 0);
		}
	}

	/**
	 * Method to get a JPagination object for the data set.
	 *
	 * @return	object	A JPagination object for the data set.
	 * @since	1.0
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Create the pagination object.
		jimport('migur.library.pagination');
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new MigurPagination($this->getTotal(), $this->getStart(), $limit);
		$page->model = $this;
		// Add the object to the internal cache.
		$this->cache[$store] = $page;


		return $this->cache[$store];
	}

	/**
	 * There we change the default behavior.
	 * Build an SQL query to load the list data.
	 * Allow to use not only common request. If you have setted $this->query before
	 * you call getItems then the SQL request you have setted before will be executed.
	 * As example
	 *    $model->setUnsubscribedQuery();
	 *    $model->getItems();
	 *
	 * If not then the default query will be executed.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	protected function getListQuery()
	{
		if (empty($this->query)) {
			$this->setDefaultQuery();
		}
		return $this->query;
	}

	/**
	 * There we change default behavior.
	 * Build an SQL query to load the list data.
	 * Allow to use not only common request. If you have setted $this->query before
	 * you call getItems then the SQL request you have setted before will be executed.
	 * As example
	 *    $model->setUnsubscribedQuery();
	 *    $model->getItems();
	 *
	 * If not then the default query will be executed.
	 *
	 * @param   string $search - the search string from client form data
	 * @param   char   $separator - the separator
	 *
	 * @return	array
	 * @since	1.0
	 */
	protected function _explodeSearch($search, $separator = ':')
	{
		return explode($separator, $search, 1);
	}

	/**
	 * Fetches items regardles of model's state.
	 * You can set your own filters for fetching.
	 *
	 * @param $options array
	 * @return array | null
	 */
	public function fetchItems($options = array())
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');

		$query->from($db->quoteName($this->getTable()->getTableName()) . ' AS a');

		if (!empty($options['filters'])) {
			foreach($options['filters'] as $name => $value) {
				$query->where($db->quoteName($name) . '=' . $db->quote($value));
			}
		}

		if (!empty($options['ordering'])) {
			$query->order($db->quoteName($options['ordering'][0]) . (!empty($options['ordering'][1])? ' '.$options['ordering'][1] : ''));
		}

		// echo $query; die;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   12.2
	 * @throws  Exception
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		$name = !empty($name)? $name : $this->tableClassName;
		$prefix = !empty($prefix)? $prefix :
			(!empty($this->tableClassPrefix)? $this->tableClassPrefix : 'Table');

		return parent::getTable($name, $prefix, $options);
	}
}

