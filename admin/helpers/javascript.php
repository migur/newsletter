<?php

/**
 * The javascript helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

class JavascriptHelper
{

	public static $extension = 'com_newsletter';

	/**
	 * Create a json representation of an object
	 * and add it to Document
	 *
	 * @param <type> $name - name of var
	 * @param <type> $obj  - data object
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function addObject($name, $obj, $isGlobal = false)
	{
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration(($isGlobal? '' : 'var ') . $name . ' = ' . json_encode($obj) . ';');
	}

	/**
	 * Add PHP primitive variable to Document
	 *
	 * @param <type> $name - name of var
	 * @param <type> $obj  - data object
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function addStringVar($name, $data)
	{
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('var ' . $name . ' = "' . addslashes($data) . '";');
	}

}