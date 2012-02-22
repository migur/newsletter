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

JLoader::import('tables.log', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelLogs extends JModelList
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
		$config['filter_fields'] = array(
			'log_id',
			'l.message','message',
			'l.category','category',
			'l.date','date',
		);

		//$this->context = 'logs';
		
		parent::__construct($config);
	}
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('l.*');
		$query->from('`#__newsletter_logs` AS l');

		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}

		$dateFrom = $this->getState('filter.dateFrom');
		$dateTo   = $this->getState('filter.dateTo');
		$category = $this->getState('filter.category');
		$search   = $this->getState('filter.search');
		$priority = $this->getState('filter.priority');

		// Filter by priority
		if (!empty($priority)) {
			switch($priority) {
				case 'error' : 
					$query->where('priority <= 8');
					break;
				case 'notification' : 
					$query->where('(priority >= 16 AND priority <= 64)');
					break;
				case 'debug' : 
					$query->where('priority >= 128');
					break;
			}
		}
		
		// Filter by from to date
		if (!empty($dateFrom)) {
			$query->where('l.date >= '.$db->quote($dateFrom));
		}

		// Filter by from to date
		if (!empty($dateTo)) {
			$query->where('l.date <= '.$db->quote($dateTo));
		}
			
		// Filter by search in message or category
		if (!empty($category)) {
			$query->where('l.category = '.$db->quote($category));
		}
		
		// Filter by search in message or category
		if (!empty($search)) {
			$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
			$query->where(
				'(l.message LIKE ' . $search . 
				' OR l.category LIKE ' . $search . ')'
			);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn) . ', log_id '.$orderDirn);

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
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
		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * 
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'Log', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
		$this->setState('filter.category', $category);

		$dateFrom = $this->getUserStateFromRequest($this->context . '.filter.dateFrom', 'filter_dateFrom', '');
		$this->setState('filter.dateFrom', $dateFrom);
		
		$dateTo = $this->getUserStateFromRequest($this->context . '.filter.dateTo', 'filter_dateTo', '');
		$this->setState('filter.dateTo', $dateTo);

		$priority = $this->getUserStateFromRequest($this->context . '.filter.priority', 'filter_priority', '');
		$this->setState('filter.priority', $priority);
		
		// List state information.
		parent::populateState('l.date', 'desc');
	}

	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getCategories()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('category')
			->from('`#__newsletter_logs`')
			->group('category');
		
		$query->order('category asc');

		//echo nl2br(str_replace('#__','jos_',$query));
		$db->setQuery($query);
		$res = $db->loadObjectList();
		
		$result = array();
		foreach($res as $row) {
			$result[] = $row->category;
		}
		
		return $result;
	}

	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getPriorities()
	{
		return 
			array(
				array(
					'text'  => 'COM_NEWSLETTER_ERROR',
					'value' => 'error'),
				array(
					'text'  => 'COM_NEWSLETTER_NOTIFICATION',
					'value' => 'notification'),
				array(
					'text'  => 'COM_NEWSLETTER_DEBUG',
					'value' => 'debug'));	
	}	
}
