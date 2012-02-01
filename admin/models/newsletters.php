<?php

/**
 * The newsletter model. Implements the standard functional for newsletters list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Class of newsletters model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelNewsletters extends JModelList
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
				'newsletter_id', 'n.id',
				'name', 'n.name',
				'se', 'n.name',
				'alias', 'n.alias',
                                'sent_to',
				'ordering', 'n.ordering',
				'language', 'n.language',
				'checked_out', 'n.checked_out',
				'checked_out_time', 'n.checked_out_time',
				'created', 'n.created',
				'sent_started', 'n.sent_started'
			);
		}

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
		$query->select(
			$this->getState(
				'list.select',
				'n.newsletter_id AS id, n.name, n.alias, n.type,' .
				'n.checked_out AS checked_out,' .
				'n.checked_out_time AS checked_out_time,' .
				'n.ordering AS ordering,' .
				'n.language, n.sent_started, "---" AS sent_to'
			)
		);
		$query->from('`#__newsletter_newsletters` AS n');
		// 2 is system internal newsletters. No need to show it.
		$query->where('(category = 0 OR category IS NULL)');
		
		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}
		unset($this->filtering);


		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('n.newsletter_id = ' . (int) substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where('(n.name LIKE ' . $search . ' OR n.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('n.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

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
	public function getTable($type = 'Newsletter', $prefix = 'NewsletterTable', $config = array())
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
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		if ($search == "Search...") {
			$search = "";
		}
		$this->setState('filter.search', $search);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_newsletter');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('name', 'asc');
	}

	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getSendable()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('DISTINCT n.*');
		$query->from('#__newsletter_newsletters AS n');
		$query->where('UNIX_TIMESTAMP(n.sent_started) = 0 OR n.sent_started IS NULL');
		$query->order('name');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		return $res;
	}

	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function getUsedInQueue()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('DISTINCT n.smtp_profile_id');
		$query->from('#__newsletter_newsletters AS n');
		$query->join('', '#__newsletter_queue AS q ON q.newsletter_id = n.newsletter_id');
		$query->where('q.state = 1');
		$query->order('n.smtp_profile_id');
		$db->setQuery($query);
		$spids = $db->loadAssocList(null, 'smtp_profile_id');
		
		$res = array();
		$ids = array();
		foreach($spids as $spid){
			
			$model = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
			$model->load($spid);
			
			if (!in_array($model->getId(), $ids)) {
				$res[] = $model;
				$ids[] = $model->getId();
			}
		}
		
		return $res;
	}
}