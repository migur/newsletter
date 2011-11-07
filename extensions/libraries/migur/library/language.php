<?php

/**
 * The implementation of the multiform toolbar
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

//TODO: It must be removed. The multiform functionality should be implemented
// with submitbutton.js

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.language.language');

/**
 * Class that implements the multiform toolbar
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurLanguage extends JLanguage
{
	
	/**
	 * Returns a language object.
	 *
	 * @param   string   $lang   The language to use.
	 * @param   boolean  $debug  The debug mode.
	 *
	 * @return  JLanguage  The Language object.
	 *
	 * @since   11.1
	 */
	public static function getInstance($lang, $debug=false)
	{
		if (!isset(self::$languages[$lang.$debug])) {
			self::$languages[$lang.$debug] = new MigurLanguage($lang, $debug);
		}

		return self::$languages[$lang.$debug];
	}
	
	/**
	 * Loads a language file and converts it to file.
	 *
	 * This method will not note the successful loading of a file - use load() instead.
	 *
	 * @param   string   $filename   The name of the file.
	 * @param   string   $extension  The name of the extension.
	 * @param   boolean  $overwrite  Not used??
	 *
	 * @return  boolean  True if new strings have been added to the language
	 *
	 * @see     JLanguage::load()
	 * @since   11.1
	 */
	public function fileToArray($extension, $basePath = JPATH_BASE, $lang = null)
	{
		if (! $lang) {
			$lang = $this->lang;
		}

		$path = self::getLanguagePath($basePath, $lang);
		
		
		$internal = $extension == 'joomla' || $extension == '';
		$filename = $internal ? $lang : $lang . '.' . $extension;
		$filename = "$path/$filename.ini";
		
		if (file_exists($filename)) {
			return $this->parse($filename);
		}
		
		return false;
	}
}
