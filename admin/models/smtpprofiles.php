<?php

/**
 * The SMTPprofiles list model. Implements the standard functional for SMTPprofiles list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.entity.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of SMTPprofiles list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSmtpprofiles extends MigurModelList
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
				'smtp_profile_name', 'a.smtp_profile_name',
				'from_name', 'a.from_name',
				'from_email', 'a.from_email',
				'reply_to_email', 'a.reply_to_email',
				'reply_to_name', 'a.reply_to_name',
				'smtp_server', 'a.smtp_server',
				'smtp_port', 'a.smtp_port',
				'is_ssl', 'a.is_ssl',
				'pop_before_smtp', 'a.pop_before_smtp',
				'username', 'a.username',
				'password', 'a.password'
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
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		}

		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.smtp_profile_name', 'asc');
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

		$query->from('#__newsletter_smtp_profiles AS a');

		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (in_array($published, array('0', '1'))) {
			$query->where('a.state = ' . (int) $published);
		}

		// Filter by search in title.
		$search = $this->_explodeSearch($this->getState('filter.search'));
		switch ($search[0]) {
			case 'id':
			case 'default':
				break;
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'smtp_profile_name');
		$orderDirn = $this->state->get('list.direction', 'asc');
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$this->query = $query;
	}

	
	/**
	 * Get all SMTP profiles
	 */
	public function getAllItems($includeDefault = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__newsletter_smtp_profiles AS a');
		$query->order('is_joomla DESC, a.smtp_profile_name ASC');
		// Get the options.
		$db->setQuery($query);

		$list = $db->loadObjectList();

		// Add default SMTP profile to list as copy
		if ($includeDefault) {
			$params = JComponentHelper::getParams('com_newsletter');
			$defaultId = $params->get('general_smtp_default');
			foreach($list as $item) {
				if ($item->smtp_profile_id == $defaultId || ($defaultId == 0 && $item->is_joomla == 1)) {
					$defItem = clone($item);
					$defItem->smtp_profile_id = (string)NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID;
					$list[] = $defItem;
					break;
				}
			}
		}
		
		return $list;
	}
}
