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

	protected $_queryType = null;

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
	protected function populateState($ordering = null, $direction = null)
	{
		/**
		 * If 'form' variable is mismatched with model name
		 * then set data from user state
		 * because this post data is addressed to another model
		 */
		if ($this->context) {
			// If the context is set, assume that stateful lists are used.
			$app = JFactory::getApplication();
			$form = JRequest::getVar('form');
			$name = $this->getName();
			if ($form != $name) {
				// Get data from user state
				// global for all lists(models)
				$limit = $app->getUserState($this->context . '.limit');
				if (!$limit) {
					$limit = $app->getUserState('global.list.limit');
					$limit = ($limit) ? $limit : $app->getCfg('list_limit');
				}
				$lstart = $app->getUserState($this->context . '.limitstart');

				// Check if the ordering field is in the white list, otherwise use the incoming value.
				$order = $app->getUserState($this->context . '.ordercol');
				// Check if the ordering direction is valid, otherwise use the incoming value.
				$dir = $app->getUserState($this->context . '.orderdirn');
			} else {
				// global for all lists(models)
				$limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', $app->getCfg('list_limit'));
				$lstart = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
				// Check if the ordering field is in the white list, otherwise use the incoming value.
				$order = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
				// Check if the ordering direction is valid, otherwise use the incoming value.
				$dir = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);
			}

			//// Update user state and model state
			$limitstart = ($limit != 0 ? (floor($lstart / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);

			if (!in_array($order, $this->filter_fields)) {
				$order = $ordering;
				$app->setUserState($this->context . '.ordercol', $order);
			}
			$this->setState('list.ordering', $order);

			if (!in_array(strtoupper($dir), array('ASC', 'DESC', ''))) {
				$value = $direction;
				$app->setUserState($this->context . '.orderdirn', $value);
			}
			$this->setState('list.direction', $dir);

			$this->setState('list.limit', $limit);
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

}

