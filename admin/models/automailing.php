<?php

/**
 * The newsletter model. Implements the standard functional for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of newsletter model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelAutomailing extends JModelAdmin
{

	protected $_context;

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
	public function getTable($type = 'Automailing', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_newsletter.automailing', 'automailing', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.automailing.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}
	
	public function getTargets() 
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('automailing_name, automailing_type, time_start, time_offset, status, sent, n.name AS newsletter_name');
		$query->from('#__newsletter_automailing_items AS ai');
		$query->join('', '#__newsletter_newsletters AS n ON n.newsletter_id = ai.newsletter_id');
		$query->join('', '#__newsletter_automailings AS a ON a.automailing_id = ai.automailing_id');

		if (!empty($this->automailingId)) {
			$query->where('ai.automailing_id='.(int)$this->automailingId);
		}
		
		// Filter by search in title.
//		$search = $this->getState('filter.search');
//		if (!empty($search)) {
//			$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
//			$query->where('(a.timeoffset LIKE ' . $search . ')');
//		}

		// Add the list ordering clause. 
		// Need to be setted in populateState
//		$orderCol = $this->state->get('list.ordering');
//		$orderDirn = $this->state->get('list.direction');
		$query->order('time_offset ASC');

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		return $query;
	}
}
