<?php

/**
 * The automailings list model. Implements the standard functionality for automailings list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.simplexml');

/**
 * Class of automailings list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelAutomailings extends MigurModelList
{
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
			$search = $app->getUserState($this->context . '.filter.search');
			$published = $app->getUserState($this->context . '.filter.published');
			$published = ($published) ? $published : '';
		} else {
			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			if ($search == "Search...") {
				$search = "";
			}
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		}

		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.automailing_name', 'asc');
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
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_automailings AS a');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
			$query->where('(a.automailing_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause. 
		// Need to be setted in populateState
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}
