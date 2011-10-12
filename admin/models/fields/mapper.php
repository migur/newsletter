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
 * Mapper Field class for the Migur Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldMapper extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'mapper';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup(span with mapped value).
	 * @since	1.0
	 */
	protected function getInput()
	{

		$opts = array();
		foreach ($this->element->children() as $option) {

			// Only add <option /> elements.
			if ($option->getName() == 'option') {
				$opts[$option->getAttribute('value')] = $option->data();
			}
		}

		$data = "";
		if ($this->value !== null && isset($opts[$this->value])) {
			$data = JText::_($opts[$this->value]);
		}
		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['value'] ? ' value="' . JText::_($this->element['value']) . '"' : '';
		$attr .= $this->element['name'] ? ' name="' . JText::_($this->element['name']) . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onclick'] ? ' onclick="' . (string) $this->element['onchange'] . '"' : '';

		// Create a regular list.
		$html = '<span ' . $attr . '>' . $data . '</span>';

		return $html;
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	null
	 * @since	1.0
	 */
	protected function getOptions()
	{
		return null;
	}

}
