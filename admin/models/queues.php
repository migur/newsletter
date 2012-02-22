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

JLoader::import('tables.queue', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelQueues extends JModelList
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
			'queue_id',
			'n.name',
			's.name',
			's.email',
			'q.create',
			'q.state',
		);

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
		$query->select('q.*, n.name AS newsletter_name, s.email AS subscriber_email, s.name AS subscriber_name');
		$query->from('`#__newsletter_queue` AS q');
		$query->join('left', '`#__newsletter_newsletters` AS n ON n.newsletter_id = q.newsletter_id');
		$query->join('left',
			'(SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			LEFT JOIN #__users AS u ON (s.user_id = u.id)
			WHERE s.user_id = 0 OR u.id IS NOT NULL OR s.email != ""

			UNION

			SELECT s.subscriber_id, COALESCE(u.name, s.name) AS name, COALESCE(u.email, s.email) AS email, COALESCE(IF(u.block IS NULL, NULL, 1-u.block), s.state) AS state, u.id AS user_id
			FROM #__newsletter_subscribers AS s
			RIGHT JOIN #__users AS u ON (s.user_id = u.id)) AS s ON s.subscriber_id = q.subscriber_id');

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
				$query->where('q.queue_id = ' . (int) substr($search, 3));
			} else {
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where(
					'(n.name LIKE ' . $search . 
					' OR s.name LIKE ' . $search . 
					' OR s.email LIKE ' . $search . ')'
				);
			}
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
	public function getTable($type = 'Queue', $prefix = 'NewsletterTable', $config = array())
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

		// Load the parameters.
		$params = JComponentHelper::getParams('com_newsletter');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('q.created', 'asc');
	}
	
	
	/**
	 * Get all items that will be sent with this smtp profile and status=1
	 * 
	 * @param type $id
	 * @return type 
	 */
	public function getUnsentSidNidBySmtp($id, $limit = 0) 
	{

		$smtpModel = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
		$smtpModel->load($id);
		
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT q.newsletter_id, q.subscriber_id');
		$query->from('`#__newsletter_queue` AS q');
		$query->join('left', '`#__newsletter_newsletters` AS n ON n.newsletter_id = q.newsletter_id');

		$and = 'n.smtp_profile_id='.(int)$id;
		
		if ($smtpModel->isDefaultProfile()) {
			$and .= ' OR n.smtp_profile_id='.(int)NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID;
		}
		
		// Back compatibility
		if ($smtpModel->isJoomlaProfile()) {
			$and .= ' OR n.smtp_profile_id='.(int)NewsletterModelEntitySmtpprofile::JOOMLA_SMTP_ID;
		}
		
		$query->where('q.state=1 AND ('. $and .')');
		$db->setQuery($query, 0, $limit);
		
		return $db->loadObjectList();
	}
	
	
	public function updateState($state, $nid, $sid)
	{
		$db = $this->getDbo();
		
		$db->setQuery(
				'UPDATE #__newsletter_queue SET state='.$state
				.' WHERE newsletter_id=' . $nid
				.' AND subscriber_id=' . $sid);
		return $db->query();
	}
	
	public function getItemsByFilter($params)
	{
		$params = (array)$params;
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__newsletter_queue');
		
		if (!empty($params)) {
			foreach($params as $name => $val) {
				$query->where($name.'='.$db->quote($val));
			}
		}	
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
