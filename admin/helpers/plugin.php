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
	public static function getInfo($plugin, $native = false, $group = 'migur')
	{
		$root = (!$native) ? JPATH_COMPONENT_ADMINISTRATOR . DS . 'extensions' . DS . 'plugins' : JPATH_SITE . DS . 'plugins' . DS . $group;
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
		return $xml;
	}

	/**
	 * Gets the list of ALL supported plugins
	 * 
	 * @return array - the list of supported modules 
	 */
	public function getSupported($params = array(), $namespace = '')
	{
		$extensions = array_merge(
				self::getNativeSupported(),
				self::getLocallySupported()
		);

		for($i = 0; $i < count($extensions); $i++) {

			$item = $extensions[$i];
			// Add the info about module
			$xml = self::getInfo($item->extension, $item->native);
			
			if (!self::namespaceCheckOccurence($namespace, (string)$xml->namespace)) {
				unset($extensions[$i]);
				continue;
			}
			
			if (!isset($params['withoutInfo'])) {
				$item->xml = $xml;
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
			. $db->Quote(NewsletterTableNExtension::TYPE_PLUGIN) . ' AS type, '
			. '\'0\' AS native'
		);
		$query->from('`#__newsletter_extensions` AS a');

		// Filter by module
		$query->where('a.type = ' . $db->Quote(NewsletterTableNExtension::TYPE_PLUGIN));
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
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			"extension_id, `name` as title, element as extension, params, type, ".
			"'1' AS native"
		);
		$query->from('`#__extensions` AS a');

		// Filter by module
		$query->where("a.type = 'plugin'");
		$query->where("a.folder = 'migur'");
		$query->order('a.name ASC');

		//echo nl2br(str_replace('#__','jos_',$query));
		return $db->setQuery($query)->loadObjectList();
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

/* Unused
        public static function triggerBefore() {
            
            list($controller, $action) = explode(JRequest('task', ''));
            
            if (empty($controller)){
                $controller = 'default';
            }
            
            if (empty($action)){
                $action = 'default';
            }
            
            self::$controller = strtolower($controller);
            self::$action = strtolower($action);

            $app = JFactory::getApplication();
            $app->triggerEvent('onMigurNewsletterBefore'.ucwords(self::$controller).ucwords(self::$action), array());
            
        }
        
        
        public static function triggerAfter() {
            
            $app = JFactory::getApplication();
            $app->triggerEvent('onMigurNewsletterAfter'.ucwords(self::$controller).ucwords(self::$action), array());
        }
 */
	
	public static function namespaceCheckOccurence($requestedNamespace = '', $extNamespace = '')
	{
		// If $requestedNamespace is empty then allow for all
		if (empty($requestedNamespace)) {
			return true;
		}
		// If extension does not has the namespace and 
		// the requested is not empty then denie it
		if (empty($extNamespace) && !empty($requestedNamespace)) {
			return false;
		}
		
		$req = explode('.', $requestedNamespace);
		$ext = explode('.', $extNamespace);
		
		for($i=0; $i < count($req); $i++) {
			if ($req[$i] != $ext[$i]) {
				return false;
			}
		}
		
		return true;
	}
}
