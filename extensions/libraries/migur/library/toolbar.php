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
	throw new Exception(JText::_("MIGUR library wasn't found."));
}

/**
 * Class that implements the multiform toolbar
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurToolBar extends JToolBar
{

	protected $_formName = '';

	protected $_actionPrefix = '';

	protected $_options = array();

	protected static $_globalButtonPath = array();

	/**
	 * The constructor of a class
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($name = 'toolbar', $options = array())
	{
		parent::__construct($name);

		$this->_options = (array) $options;

		$this->_options['formName'] = !empty($options['form']) ? $options['form'] : $name . 'Form';
		$this->_options['actionPrefix'] = !empty($options['actionPrefix'])? $options['actionPrefix'] : '';
	}

	/**
	 * Returns the global JToolBar object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param	string		$name  The name of the toolbar.
	 *
	 * @return	JToolBar	The MigurToolBar object.
	 * @since   1.0
	 */
	public static function getInstance($name = 'toolbar', $options = array())
	{
//		static $instances;

		if (!isset(self::$instances)) {
			self::$instances = array();
		}

		if (empty(self::$instances[$name])) {

			self::$instances[$name] =
				empty($options['migurInstance'])?
					new JToolBar($name) :
					new MigurToolBar($name, $options);

			self::$instances[$name]->addButtonPath(self::$_globalButtonPath);
		}

		return self::$instances[$name];
	}

	/**
	 * Changes standard behavior.
	 *
	 * @param	object	A param tag node.
	 * @param	string	The control name.
	 *
	 * @return	array	Any array of the label, the form element and the tooltip.
	 * @since   1.0
	 */
	public function renderButton(&$node)
	{
		$html = parent::renderButton($node);
		$formName = $this->_formName;

		if (!empty($this->_options['preserveJCallback'])) {
			return $html;
		}

		return preg_replace(
			array(
				"/Joomla\.submitbutton\(([^)]*)\)/",
				"/adminForm/"
			),
			array(
				"Joomla.submitform($1, document.{$formName}, this)",
				$formName
			),
			$html
		);
	}

	public static function addGlobalButtonPath($path)
	{
		// Just force path to array.
		settype($path, 'array');

		// Loop through the path directories.
		foreach ($path as $dir)
		{
			// No surrounding spaces allowed!
			$dir = trim($dir);

			// Add trailing separators as needed.
			if (substr($dir, -1) != DIRECTORY_SEPARATOR)
			{
				// Directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// Add to the top of the search dirs.
			array_push(self::$_globalButtonPath, $dir);
		}
	}
}
