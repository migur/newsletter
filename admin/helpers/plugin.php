<?php

/**
 * The newsletter plugin helper
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

/**
 * Plugin helper class
 *
 * @static
 * @since		1.0
 */
abstract class MigurPluginHelper
{
	static $_plugins;

	/**
	 * Gets the data from module config file
	 *
	 * @param <type> $plugin - module identificator (mod_*)
	 * @param <type> $namespace - 'joomla' or 'component'
	 *
	 * @return JObject - data
	 * @since 1.0
	 */
	public static function getInfo($plugin, $native = false)
	{
		$root = (!$native) ? JPATH_COMPONENT_ADMINISTRATOR . DS . 'extensions' . DS . 'plugins' : JPATH_SITE . DS . 'plugins';
		$path = JPath::clean($root . DS . $plugin . DS . $plugin . '.xml');
		if (file_exists($path)) {
			$xml = simplexml_load_file($path);
		} else {
			$xml = new JObject();
		}

		/* Set the default values */
//		foreach (self::$defaults as $name => $item) {
//			if (!isset($xml->{$name})) {
//				$xml->{$name} = JText::_($item);
//			}
//		}
		//var_dump($path, $xml); die();
		return $xml;
	}

	/**
	 * Gets the list of ALL supported plugins
	 * 
	 * @return array - the list of supported modules 
	 */
	public function getSupported($params = array())
	{
		$extensions = array_merge(
				self::getNativeSupported(),
				self::getLocallySupported()
		);

		foreach ($extensions as &$item) {

			// Add the info about module
			if (!isset($params['withoutInfo'])) {
				$item->xml = self::getInfo($item->extension, $item->native);
			}

			if (empty($item->params)) {
				$item->params = "{}";
			}
			$item->params = (object) json_decode($item->params, true);

			//HOTFIX: To match the properties with the result object of &_load. Need to implement the MODULE object.
			$item->module = $item->extension;
//			$result->id = 0;
//			$result->title = '';
//			$result->module = $name;
//			$result->position = '';
//			$result->content = '';
//			$result->showtitle = 0;
//			$result->control = '';
//			$result->params = '';
//			$result->user = 0;
			// Try to find only one module
			if (isset($params['extension_id']) && isset($params['native']) && $params['extension_id'] == $item->extension_id && $params['native'] == $item->native) {
				return array($item);
			}
		}

		// If we are here then the necessary module could not found
		if (!empty($params['extension_id']) && !empty($params['native'])) {
			JError::raiseError(E_ERROR, "The module " . $params['extension_id'] . " could not found in the list of supported modules (native = " . $params['native'] . ")");
		}

		return $extensions;
	}

	/**
	 * Gets the list of supported local plugins.
	 * Gets the full info about each one.
	 *
	 * @return array - list of supported modules
	 * @since  1.0
	 */
	public function getLocallySupported()
	{
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'extension_id, title, extension, params, '
			. $db->Quote(NewsletterTableExtension::TYPE_PLUGIN) . ' AS type, '
			. '\'0\' AS native'
		);
		$query->from('`#__newsletter_extensions` AS a');

		// Filter by module
		$query->where('a.type = ' . $db->Quote(NewsletterTableExtension::TYPE_PLUGIN));
		$query->order('a.title ASC');

		//echo nl2br(str_replace('#__','jos_',$query));
		return $db->setQuery($query)->loadObjectList();
	}

	/**
	 * Gets the list of supported native modules (not plugins).
	 * Gets the full info about each one.
	 *
	 * @return array - list of supported modules
	 * @since  1.0
	 */
	public function getNativeSupported()
	{
		return array();
	}

	/**
	 * Gets the list of plugins used in newsletter
	 *
	 * @param  integer $uid
	 *
	 * @return array
	 * @since  1.0
	 */
	static public function getUsedInNewsletter($uid)
	{
		if (empty($uid)) {
			return array();
		}	
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__newsletter_extensions AS e');
		$query->join('', '#__newsletter_newsletters_ext AS ne ON e.extension_id = ne.extension_id');
		$query->where('ne.newsletter_id='.$db->quote((int)$uid));
		$query->where('type = "2"');

		// Set the query
		$db->setQuery($query);
		$objs = $db->loadObjectList();

		// Remove inactive plugins
		foreach($objs as $idx => $obj) {
			$obj->params = (object)json_decode($obj->params);
			if (empty($obj->params->active)) {
				unset($objs[$idx]);
			}
		}
		return $objs;
	}

	/**
	 * Trigger the action of a plugin.
	 *
	 * @param	object	A module object.
	 * @param	array	An array of attributes for the module (probably from the XML).
	 *
	 * @return	strign	The HTML content of the module output.
	 * @since   1.0
	 */
	public static function trigger($pluginName, $action, $params, $document)
	{
		self::getInstance($pluginName);
		return self::$_plugins[$plugin]->$action($params, $document);
	}
	
	
	/**
	 * Trigger the action of a plugin.
	 *
	 * @param	object	A module object.
	 * @param	array	An array of attributes for the module (probably from the XML).
	 *
	 * @return	strign	The HTML content of the module output.
	 * @since   1.0
	 */
	public static function getInstance($pluginName)
	{
		if (empty(self::$_plugins[$pluginName])) {

			require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'extensions' . DS . 'plugins' . DS . $pluginName . DS . $pluginName . '.php';
			self::$_plugins[$plugin] = new $pluginName;
			return self::$_plugins[$plugin];
		}
		
		return null;
	}

}
