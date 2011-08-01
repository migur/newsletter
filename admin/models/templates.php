<?php

/**
 * The templates list model. Implements the standard functionality for templates list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.utilities.simplexml');

/**
 * Class of templates list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelTemplates extends MigurModelList
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
				't_style_id', 'a.t_style_id',
				'template', 'a.template',
				'title', 'a.title',
				'params', 'a.params'
			);
		}

		parent::__construct($config);
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
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);

		$query->from('#__newsletter_template_styles AS a');

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'template:') === 0) {
				$search = $db->Quote('%' . $db->getEscaped(substr($search, 9), true) . '%');
				$query->where('(a.template LIKE ' . $search . ')');
			} else {
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where('(a.template LIKE ' . $search . ' OR a.title LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol == 'a.ordering' || $orderCol == 'a.name') {
			$orderCol = 'title ' . $orderDirn;
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Get all (standard and custom) templates
	 *
	 * @return	array list of the templates
	 * @since	1.0
	 */
	public function getAllTemplates()
	{

		$customs = $this->getItems();
		$standards = $this->getStandardTemplates();

		$orderDirn = $this->state->get('list.direction');
		$search = $this->getState('filter.search');
		$type = $this->getState('filter.published');

		switch ($type) {
			case '1':
				$merged = $standards;
				break;
			case '2':
				$merged = $customs;
				break;
			default:
				$merged = array_merge($standards, $customs);
		}

		$len = count($merged);
		for ($i = 0; $i < $len; $i++) {

			if (empty($search) || strpos($merged[$i]->title, $search) !== false) {
				for ($j = $i; $j < $len; $j++) {
					if ($orderDirn == 'asc' && $merged[$i]->title > $merged[$j]->title ||
						$orderDirn == 'desc' && $merged[$i]->title < $merged[$j]->title) {

						$buff = $merged[$i];
						$merged[$i] = $merged[$j];
						$merged[$j] = $buff;
					}
				}
			}
		}
		return $merged;
	}

	/**
	 * Get standard templates.
	 *
	 * @return	array list of the templates
	 * @since	1.0
	 */
	public function getStandardTemplates()
	{
		$standards = array();

		$path = JPATH_COMPONENT . '/extensions/templates/';
		$filenames = JFolder::files($path, '^.*\.xml$');
		if ($filenames === false) {
			JError::raiseError(500, implode("\n", array("Path $path not found")));
		}

		foreach ($filenames as $item) {
			$xml = new JSimpleXML;
			$xml->loadFile($path . $item);
			$title = trim($xml->document->information[0]->name[0]->_data);
			$standards[] = (object) array(
					't_style_id' => 'standard',
					'template' => $item,
					'title' => $title,
					'params' => '{}'
			);
			unset($xml);
		}

		return $standards;
	}

}
