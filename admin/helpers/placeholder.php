<?php

/**
 * The palceholder helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class PlaceholderHelper
{
	/*
	 * The allowed types of a letter
	 */

	public static $_instances = array(array());

	/*
	 * The container for placeholders
	 */
	public static $placeholders = array();
	
	/*
	 * Tags to enclose placeholders
	 */
	public static $openedTag = '[';
	public static $closedTag = ']';

	protected static $_initialized = false;
	
	/**
	 * Get the instance of a placeholder
	 * If the class for placeholder is not founded
	 * then use the "Simple" placeholder class
	 *
	 * @param  string $name - the suffic of class (name of placeholder)
	 * @param  string $namespace - the HTML or 'plain'
	 *
	 * @return string
	 * @since 1.0
	 */
	public function getInstance($name, $namespace)
	{

		$name = strtolower($name);
		$namespace = strtolower($namespace);

		if (empty($name) || empty($namespace)) {
			Jerror::throwError('The name or namespace of placeholder is missing');
		}

		/*
		 * Try to get class of placeholder.
		 * If 'class' is not determined then use the $name as name of class of placeholder
		 */
		$pdata = !empty(self::$placeholders[$name])? self::$placeholders[$name] : array();
		$class = empty($pdata['class']) ? $name : $pdata['class'];

		if (isset(self::$_instances[$namespace][$class])) {
			return self::$_instances[$namespace][$class];
		}

		$path = 'migur' . DS . 'library' . DS . 'mailer' . DS . 'document' . DS .
			$namespace . DS . 'renderer' . DS . 'placeholder' . DS;

		if (!JLoader::import($path . $class, JPATH_LIBRARIES)) {

			$class = 'simple';
			if (!JLoader::import($path . $class, JPATH_LIBRARIES)) {

				JError::raiseError(500, 'File or class not found for ' . $class . ', ' . $namespace);
			}
		}
		$className = ucfirst($namespace) . 'Placeholder' . ucfirst($class);
		$obj = new $className();
		self::$_instances[$namespace][$class] = $obj;
		return $obj;
	}

	/**
	 * Initialize and render the placeholder
	 * If the class for placeholder is not founded
	 * then use the "Simple" placeholder class
	 *
	 * @param  string $name - the suffic of class (name of placeholder)
	 * @param  string $namespace - the HTML or 'plain'
	 * @param  string $data - data to render
	 * @param  string $default - default data
	 *
	 * @return string
	 * @since 1.0
	 */
	public function render($name, $namespace, $data = null, $default = null)
	{
		$pholder = self::getInstance($name, $namespace);

		if (!isset($data)) {
			$data = self::getPlaceholder($name, $default);
		}

		return $pholder->render($data, array('name'=>$name));
	}

	/**
	 * Initializes the helper
	 *
	 * @param  boolean $force - to force initialization or not
	 *
	 * @return boolean
	 * @since 1.0
	 */
	protected static function _init($force = false)
	{
		if (!self::$_initialized || $force) {

			self::$placeholders = array();
			self::$placeholders['username'] = array('data' => null, 'default' => "",);
			self::$placeholders['useremail'] = array('data' => null, 'default' => "");
			self::$placeholders['userid'] = array('data' => null, 'default' => 0);
			self::$placeholders['image_top.alt'] = array('data' => null, 'default' => 'The top image');
			self::$placeholders['image_bottom.alt'] = array('data' => null, 'default' => 'The bottom image');
			self::$placeholders['sitename'] = array('data' => null, 'default' => JFactory::getConfig()->get('sitename'));
			self::$placeholders['table_background'] = array('data' => null, 'default' => '#FFFFFF');
			self::$placeholders['text_color'] = array('data' => null, 'default' => '#000000');

			self::$placeholders['unsubscription link'] = array(
				'data' => null,
				'default' => JUri::getInstance()->toString(array('host', 'scheme')) . JRoute::_('index.php?option=com_newsletter&task=subscribe.showunsubscribe', false) . '&uid=[subscription key]&nid=[newsletter id]',
				'class' => 'link'
			);

			self::$placeholders['confirmation link'] = array(
				'data' => null,
				'default' => JUri::getInstance()->toString(array('host', 'scheme')) . JRoute::_('index.php?option=com_newsletter&task=subscribe.confirm', false) . '&id=[subscription key]',
				'class' => 'link'
			);

			self::$_initialized = true;
		}
		return true;
	}

	/**
	 * Get the current dynamic data for placeholder.
	 *
	 * @param string $name - name of placeholder without percents
	 * @param string $default - default value
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function getPlaceholder($name, $default = null)
	{

		self::_init();

		$name = strtolower($name);
		// return the data
		if (isset(self::$placeholders[$name]['data'])) {
			$val = self::$placeholders[$name]['data'];
			return $val;
		}

		// return the default data if dynamic was not found
		$val = (!empty(self::$placeholders[$name]['default']) ) ?
			self::$placeholders[$name]['default'] : $default;

		return $val;
	}

	/**
	 * Update or add the placeholder
	 *
	 * @param <string> $name - placeholder without percents
	 * @param <string> $data
	 * @param <string> $default
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function setPlaceholder($name, $data, $default = null)
	{

		self::_init();

		if (!isset(self::$placeholders[$name])) {
			self::$placeholders[$name] = array(
				'data' => null,
				'default' => null
			);
		}

		self::$placeholders[$name]['data'] = $data;
		if (!empty($default)) {
			self::$placeholders[$name]['default'] = $default;
		}
	}

	/**
	 * Update or add the placeholders
	 *
	 * @param <string> $name - array
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function setPlaceholders($array, $reset = false)
	{
		self::_init($reset);

		foreach ($array as $name => $data) {
			if (!empty($name)) {
				self::setPlaceholder($name, $data);
			}
		}
	}
	
	public static function fetchFromString($string)
	{
		$preg = "/\\".self::$openedTag."([^\\".self::$closedTag."]+)\\".self::$closedTag."/";
		
		preg_match_all($preg, $string, $matches);
		
		if (empty($matches[1])) {
			return array();
		}	

		return array_values(array_unique($matches[1]));
	}
}
