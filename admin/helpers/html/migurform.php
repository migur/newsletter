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
		$args = func_get_args();
		
		$type =    isset($args[0])? $args[0] : null;
		$domId =   isset($args[1])? $args[1] : null;
		$value =   isset($args[2])? $args[2] : null;
		$options = isset($args[3])? (array) $args[3] : array();
		
		// Load the JFormField object for the field.
		JFormHelper::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'fields');
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
