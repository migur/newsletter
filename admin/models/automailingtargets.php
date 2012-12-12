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

JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of automailings list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelAutomailingTargets extends MigurModelList
{
	public $automailingId = null;
	
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
		parent::populateState('a.time_offset', 'asc');
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
		$query->from('#__newsletter_automailing_targets');

		if (!empty($this->automailingId)) {
			$query->where('automailing_id='.(int)$this->automailingId);
		}
		
		//echo nl2br(str_replace('#__','jos_',$query)); die;
		return $query;
	}
	
	
	/**
	 * 
	 * 
	 * 
	 * @return type 
	 */
	public function getNames($aid) 
	{
		$this->automailingId = $aid;

		$items = $this->getItems();
		
		$ids = array(
			'list' => array(), 
			'subscriber' => array()
		);
		
		$names = array(
			'list' => array(), 
			'subscriber' => array()
		);
		
		foreach($items as &$item) {
			$ids[$item->target_type][] = $item->target_id;
		}	
		
		foreach($ids as $idx => &$idList) {
			
			if ($idx == 'list' && !empty($idList)) {
				
				$dbo = JFactory::getDbo();
				$query = $dbo->getQuery(true);
				$query->select('name')
					  ->from('#__newsletter_lists')
					  ->where('list_id in ('.implode(',',$idList).')');
				$dbo->setQUery($query);
				$nms = $dbo->loadAssocList(null, 'name');
				$names['list'] = array_merge($names['list'], $nms);
			}	

			if ($idx == 'subscriber' && !empty($idList)) {
				
				$dbo = JFactory::getDbo();
				$query = $dbo->getQuery(true);
				$query->select('name')
					  ->from('#__newsletter_subscribers')
					  ->where('subscriber_id in ('.implode(',',$idList).')');
				$dbo->setQUery($query);
				$names['subscriber'] = array_merge($names['subscriber'], $dbo->loadAssocList(null, 'name'));
			}	
		}
		
		return $names;
	}
	

	/**
	 * 
	 * 
	 * 
	 * @return type 
	 */
	public function getRelatedLists($aid, $usePagination = false) 
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
			  ->from('#__newsletter_lists AS l')
			  ->join('', '#__newsletter_automailing_targets AS at ON at.target_type="list" AND at.target_id = l.list_id')
			  ->where('l.state=1')
			  ->where('at.automailing_id='.(int)$aid);

		if (!empty($usePagination)) {
			$dbo->setQuery($query, $this->getStart(), $this->getState('list.limit'));
		} else {
			$dbo->setQuery($query);
		}	

		return $dbo->loadObjectList();
	}

	
	/**
	 * Gets a list of all active lists 
	 * without pagination and other limitations
	 * 
	 * @return array of objects
	 */
	public function findByAid($aid, $idonly = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			  ->from('#__newsletter_automailing_targets')
		      ->where('automailing_id='.(int)$aid);
		
		$db->setQuery($query);
		
		if (!empty($idonly)) {
			return $db->loadObjectList(null, 'am_target_id');
		}
		
		return $db->loadObjectList();
	}
	
	
}
