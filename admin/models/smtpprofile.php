<?php

/**
 * The SMTP profile model. Implements the standard functional for SMTP profile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of SMTPprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelSmtpprofile extends JModelAdmin
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
	public function getTable($type = 'Smtpprofile', $prefix = 'NewsletterTable', $config = array())
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
		$form = $this->loadForm('com_newsletter.smtpprofile', 'smtpprofile', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.smtpprofile.data', array());
		if (empty($data)) {
			$model = JModel::getInstance('SmtpProfile', 'NewsletterModelEntity');
			$smtpid = JRequest::getVar('smtp_profile_id', null);
			if ($smtpid !== null) {
				$model->load($smtpid);
			}	
			$data = $model->toArray();
		}
		return $data;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string Script files
	 */
	public function getScript()
	{
		return 'administrator/components/com_newsletter/models/forms/smtpprofile.js';
	}

}
