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

JLoader::import('models.entities.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of subscribers list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelBounceds extends MigurModelList
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
			$list = $app->getUserState($this->context . '.filter.list');
			$published = $app->getUserState($this->context . '.filter.published');
			$published = ($published) ? $published : '';
			$search = $app->getUserState($this->context . '.filter.search');
		} else {
			$list = $this->getUserStateFromRequest($this->context . '.filter.list', 'filter_list', '');
			$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
			$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
			if ($search == "Search...") {
				$search = "";
			}
		}

		$this->setState('filter.list', $list);
		$this->setState('filter.published', $published);
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	
	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is contructed only once for a given state of the model.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object
	 *
	 * @since   11.1
	 */
	protected function _getListQuery()
	{
		return null;
	}
	
	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is contructed only once for a given state of the model.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object
	 *
	 * @since   11.1
	 */
	protected function _getList($query, $limitstart=0, $limit=0)
	{
		$list = MailHelper::getBouncedList($limitstart=0, $limit=0);
		return $list;
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
	 * Returns a record count for the query
	 *
	 * @param    string  $query  The query.
	 *
	 * @return   integer  Number of rows for query
	 * @since    11.1
	 */
	protected function _getListCount($query)
	{
		$result = $this->_getList($query, $limitstart=0, $limit=0);
		return count($result);
	}
	
	public function getMailboxesForBounsecheck()
	{
		$db = JFactory::getDbo();
		
		// Get default SMTP and Mailbox profile ids
		$smtpId = MailHelper::getDefaultSmtp('idOnly');
		$mailboxId = MailHelper::getDefaultMailbox('idOnly');
		
		
		// Get mailbox ids with start mailing date
		$db->setQuery(
			' SELECT DISTINCT mp.mailbox_profile_id, min(q.created) AS startdate '. 
			' FROM #__newsletter_mailbox_profiles AS mp '.
			
			' RIGHT JOIN #__newsletter_smtp_profiles AS sp '.
				'ON (sp.mailbox_profile_id = mp.mailbox_profile_id) '.
				'OR (sp.mailbox_profile_id = '.NewsletterTableMailboxprofile::MAILBOX_DEFAULT.' AND mp.mailbox_profile_id='.$mailboxId.') '.
			
			' JOIN #__newsletter_newsletters AS n  '.
				'ON (n.smtp_profile_id = sp.smtp_profile_id) '.
				'OR (n.smtp_profile_id = '.NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID.' AND sp.smtp_profile_id='.$smtpId.') '.
			
			' JOIN #__newsletter_queue AS q ON q.newsletter_id = n.newsletter_id '.
			
			// get mailboxes for sent newsletters without errors
			' WHERE q.state = ' . NewsletterTableQueue::STATE_SENT.
			' GROUP BY mp.mailbox_profile_id'
		);

		// Get mailboxes and their start dates
		$db->setQuery(
			' SELECT DISTINCT mp.*, t.startdate'. 
			' FROM #__newsletter_mailbox_profiles AS mp '.
			' JOIN ('.(string)$db->getQuery().') as t ON mp.mailbox_profile_id=t.mailbox_profile_id');
		$result = $db->loadAssocList();
		
		foreach($result as &$mb) {
			if (!empty($mb['data'])) {
				$mb['data'] = (array)json_decode($mb['data']);
			} else {
				$mb['data'] = array(
					'lastDate' => NULL,
					'ids'=>array()
				);
			}
			
			$mb['password'] = base64_decode($mb['password']);
		}
		
		return $result;
	}
}
