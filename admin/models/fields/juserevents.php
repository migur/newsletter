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
class JFormFieldJuserEvents extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'juserevents';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of juser events).
	 * @since	1.0
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array(
			array('value' => 'on_register_user', 'text' => JText::_('COM_NEWSLETTER_LIST_EVENT_ON_REGISTER_USER')),
			array('value' => 'on_add_user_to_group', 'text' => JText::_('COM_NEWSLETTER_LIST_EVENT_ON_ADD_USER_TO_GROUP')),
			array('value' => 'on_remove_user_from_group', 'text' => JText::_('COM_NEWSLETTER_LIST_EVENT_ON_REMOVE_USER_FROM_GROUP')),
		);
		
		array_unshift($options, JHtml::_('select.option', '', '-- '.JText::_('COM_NEWSLETTER_SELECT_EVENT').' --'));

		return $options;
	}

}
