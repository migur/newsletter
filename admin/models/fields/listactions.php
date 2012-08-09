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
class JFormFieldListActions extends JFormFieldList
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
			array('value' => 'add', 'text' => JText::_('COM_NEWSLETTER_LIST_ACTION_ADD')),
			array('value' => 'remove', 'text' => JText::_('COM_NEWSLETTER_LIST_ACTION_REMOVE'))
		);
		
		array_unshift($options, JHtml::_('select.option', '', '-- '.JText::_('COM_NEWSLETTER_SELECT_ACTION').' --'));

		return $options;
	}

}
