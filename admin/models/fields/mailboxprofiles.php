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
 * mailboxprofiles Field class for the Joomla Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldMailboxprofiles extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'mailboxprofiles';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of available mailbox profiles).
	 * @since	1.0
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			'mailbox_profile_id AS value, mailbox_profile_name AS text, ' .
			'mailbox_server, mailbox_port, is_ssl, username, password'
		);
		$query->from('#__newsletter_mailbox_profiles AS a');
		$query->order('a.mailbox_profile_name');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		if (empty($options)) {
			$options = array();
		}	

		if (empty($this->element['scope']) || $this->element['scope'] != 'withoutDef') {
			array_unshift($options, JHtml::_('select.option', '-1', JText::_('COM_NEWSLETTER_SELECT_DEFAULT_MAILBOX_PROFILE')));
		} else {
			array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_NEWSLETTER_SELECT_PROFILE')));
		}

		return $options;
	}

}
