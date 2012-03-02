<?php

/**
 * The grid helper. Contain the methods to create the controls for grid.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

abstract class JHtmlMigurform
{
	/**
	 * $type, $domId, $value, $options = array(), $formName = null, $disabled = false
	 */
	public static function element()
	{
		list($type, $domId, $value, $options) = func_get_args();
		$options = (array)$options;
		
		// Load the JFormField object for the field.
		JFormHelper::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'fields');
		$field = JFormHelper::loadFieldType($type, true);
		
		// If the object could not be loaded, get a text field object.
		if ($field === false) {
			throw new Exception('Cannot load field type '.$type);
		}

		$element = new JXMLElement('<field></field>');
		$element->addAttribute('id', $domId);
		
		if (!empty($options)) {
			foreach($options as $name => $val) {
				$element->addAttribute($name, $val);
			}
		}
		
		if (!$field->setup($element, $value, null))
		{
			throw new Exception('Cannot setup field '.$type);
		}
		
		return $field->input;
	}
}
