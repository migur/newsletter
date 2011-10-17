<?php

/**
 * The extensions model. Implements the standard functional for list of extensions view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.nextension', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of extensions list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelExtensions extends MigurModelList
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
				'id', 'a.extension_id',
				'title', 'a.title',
				'params', 'a.params',
				'type', 'a.type'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
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
			if ($search == "Search...") {
				$search = "";
			}
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		}

		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	protected function setDefaultQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);

		$query->from('#__newsletter_extensions AS a');

		// Filter by search in title.
		$search = $this->_explodeSearch($this->getState('filter.search'));
		switch ($search[0]) {
			case 'id':
			case 'default':
				break;
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'title');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die();
		$this->query = $query;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function setNewsletterQuery($params)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*, ne.newsletter_id, ne.position, ne.ordering, ne.params AS particular_params'
			)
		);

		$query->from('#__newsletter_extensions AS a');
		$query->join('LEFT', "#__newsletter_newsletters_ext AS ne ON a.extension_id=ne.extension_id AND ne.newsletter_id='" . intval($params['newsletter_id']) . "'");


		// Filter by search in title.
		$search = $this->_explodeSearch($this->getState('filter.search'));
		switch ($search[0]) {
			case 'id':
			case 'default':
				break;
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'title');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die();
		$this->query = $query;
	}

	/**
	 * Get all the modules registered in DB
	 * @return array
	 * @since  1.0
	 */
	public function getModules()
	{
		$res = array();
		$extensions = $this->getItems();
		foreach ($extensions as $item) {
			if ($item->type == NewsletterTableNextension::TYPE_MODULE) {
				$item->xml = MigurModuleHelper::getInfo($item->extension);
				$res[] = $item;
			}
		}

		return $res;
	}

	/**
	 * Get all the plugins registered in DB
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getPlugins()
	{
		$res = array();
		$extensions = $this->getItems();
		foreach ($extensions as $item) {
			if ($item->type == NewsletterTableNextension::TYPE_PLUGIN) {
				$res[] = $item;
			}
		}
		return $res;
	}

}
