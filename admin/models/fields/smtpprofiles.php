<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * SMTPprofiles Field class for the Joomla Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldSmtpprofiles extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'smtpprofiles';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of available SMTP profiles).
	 * @since	1.0
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			'smtp_profile_id AS value, smtp_profile_name AS text, from_name, from_email, reply_to_email, ' .
			'reply_to_name, smtp_server, smtp_port, is_ssl, ' .
			'pop_before_smtp, username, password'
		);
		$query->from('#__newsletter_smtp_profiles AS a');
		$query->order('a.smtp_profile_name');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		if (empty($options))
			$options = array();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_NEWSLETTER_JOOMLA_MAIL_SETTINGS')));

		return $options;
	}

}
