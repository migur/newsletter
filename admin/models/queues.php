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
		$query->select('q.*, n.name AS newsletter_name, COALESCE(u.email, s.email) AS subscriber_email, COALESCE(u.name, s.name) AS subscriber_name');
		$query->from('`#__newsletter_queue` AS q');
		$query->join('left', '`#__newsletter_newsletters` AS n ON n.newsletter_id = q.newsletter_id');
		// SQL-query for gettting the users-subscibers list.
		$query->join('left', '`#__newsletter_subscribers` AS s ON s.subscriber_id = q.subscriber_id');
		$query->join('left', '`#__users` AS u ON s.user_id = u.id');

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
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
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
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
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
	public function getListToSend($options) 
	{
		$id = $options['smtpProfileId'];
		$limit = $options['limit'];
		
		$smtpModel = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
		$smtpModel->load($id);
		
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Let's add is_sent=0 for this mail if it was sent earlier(q.status IN (0,2)) and 1 otherwise
		$query->select('DISTINCT q.newsletter_id, q.subscriber_id, CASE WHEN q.state=1 THEN 0 ELSE 1 END AS is_sent'); 
		$query->from('`#__newsletter_queue` AS q');
		$query->join('', '`#__newsletter_newsletters` AS n ON n.newsletter_id = q.newsletter_id');
		$query->join('', '`#__newsletter_subscribers` AS s ON s.subscriber_id = q.subscriber_id');
		$query->join('left', '`#__newsletter_lists` AS l ON l.list_id = q.list_id');
		$query->where('(l.state = 1 OR l.list_id IS NULL)');
		$query->where('s.state = 1');
		$query->where('(l.state = 1 OR l.list_id IS NULL)');
		
		//  Add filter to cut off unconfirmed users. (subscribers.confirm)
		if ($options['skipUnconfirmed']) {
			$query->join('left', '`#__newsletter_sub_list` AS sl ON s.subscriber_id = sl.subscriber_id AND l.list_id = sl.list_id');
			$query->where('(sl.confirmed = 1 OR (sl.sublist_id IS NULL AND s.confirmed = 1))');
		}	
			
		$and = 'n.smtp_profile_id='.(int)$id;
		
		if ($smtpModel->isDefaultProfile()) {
			$and .= ' OR n.smtp_profile_id='.(int)NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID;
		}
		
		// Back compatibility
		if ($smtpModel->isJoomlaProfile()) {
			$and .= ' OR n.smtp_profile_id='.(int)NewsletterModelEntitySmtpprofile::JOOMLA_SMTP_ID;
		}
		
		$query->where($and);
		
		// Group all letters by nid-sid and
		// eliminate sid-nid pair if this pair has been sent earlier
		$query->group('q.newsletter_id, q.subscriber_id');
		// If at least one letter from group was sent earlier then MIN(is_sent) > 0.
		// We need groups (nid-sid) that contain only non-sent letters (all DUPLICATE in group should be = 1).
		// So we use MIN(duplicate) = 1
		$query->having('MIN(is_sent) = 0');
		
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
	
	
	/**
	 * Set bounced ALL queue items with folowing $sid, $nid
	 * 
	 * @param type $sid
	 * @param type $nid
	 * @return type 
	 */
	public function setBounced($sid, $nid)
	{
		if (empty($sid) || empty($nid)) {
			return false;
		}

		$dbo = JFactory::getDbo();
		$dbo->setQuery(
			'UPDATE `#__newsletter_queue` '.
			'SET `state` = '.NewsletterTableQueue::STATE_BOUNCED.' '.
			'WHERE `subscriber_id` = '.(int)$sid.' '.
			'AND `newsletter_id` = '.(int)$nid
		);

		//echo $dbo->getQuery(); die; 
		return $dbo->query();
	}
	
	
	public function getSummary()
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		// Select the required fields from the table.
		$query->select('newsletter_id, SUM(sent) AS sent, COUNT(*) AS total');
		$query->from('(SELECT newsletter_id, subscriber_id, MAX(CASE WHEN state=1 THEN 0 ELSE 1 END) AS sent FROM jos_newsletter_queue AS q GROUP BY newsletter_id, subscriber_id) AS q');
		$query->group('newsletter_id');
		
		$data = $dbo->setQuery($query)->loadAssocList();
		return $data;
	}
	
	public function addMail($sid, $nid, $lid = null) 
	{
		if (empty($sid) || empty($nid)) {
			return false;
		}
		
		return $this->getTable()->save(array(
			'newsletter_id' => $nid,
			'subscriber_id' => $sid,
			'list_id'       => $lid,
			'created' => date('Y-m-d H:i:s'),
			'state'   => 1)); 
	}
	
	public function isMailExist() 
	{
		return 
			$this->getTable()->load(array(
				'newsletter_id' => $newsletterId,
				'subscriber_id' => $item['subscriber_id']));
	}
}
