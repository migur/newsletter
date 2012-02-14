<?php

/**
 * The history model. Implements the standard functional for history view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

/**
 * Class of history model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelHistory extends MigurModelList
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
				'newsletter', 'a.newsletter',
				'list', 'a.list',
				'date', 'a.date',
				'action', 'a.action'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param string $ordering  - name of ordering column
	 * @param string $direction - direction of ordering
	 *
	 * @return	void
	 * @since	1.0
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
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		}

		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.date', 'desc');
	}

	/**
	 * No cache!
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.0
	 */
	protected function getStoreId($id = '')
	{
		//TODO: Need to set the cache
		// Compile the store id.
		return $id;
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
		$action = $this->getTable('history', 'newsletterTable')->getMappingFor('action');
		$query->select("a.history_id, a.subscriber_id, a.list_id, a.newsletter_id, a.date, {$action}, a.text, n.name");
		$query->from('#__newsletter_sub_history AS a');
		$query->join('LEFT', '#__newsletter_newsletters AS n ON a.newsletter_id = n.newsletter_id');
		//$query->join('LEFT', '#__newsletter_lists AS l ON a.list_id = l.list_id');


		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'date');
		$orderDirn = $this->state->get('list.direction', 'desc');

		if ($orderCol == 'a.ordering' || $orderCol == 'a.date') {

			$orderCol = 'date ' . $orderDirn;
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		return $query;
	}

	public function setBounced($sid, $nid, $bounceType)
	{
		return JTable::getInstance('History', 'NewsletterTable')->setBounced($sid, $nid, $bounceType);
	}	
}
