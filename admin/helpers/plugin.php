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

JLoader::import('tables.nextension', COM_NEWSLETTER_PATH_ADMIN);

/**
 * Plugin helper class
 *
 * @static
 * @since		1.0
 */
abstract class NewsletterHelperPlugin
{
	static $_plugins;

	static $_lang;
	
	static $_prepared = false;
	
	/**
	 * Import all needed plugins. Add all needed handlers
	 * 
	 */
	public static function prepare()
	{
		if (self::$_prepared) {
			return;
		}
		
		$jpathComponentAdministrator = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_newsletter';
		
		// Load 'Migur' group of plugins
		JLoader::import('plugins.plugin', $jpathComponentAdministrator, '');

		JPluginHelper::importPlugin('migur');

		// Bind automailing to several events
		JLoader::import('plugins.plugins.automail', $jpathComponentAdministrator);
		JFactory::getApplication()->registerEvent('onMigurAfterSubscribe', 'plgMigurAutomail');
		JFactory::getApplication()->registerEvent('onMigurAfterSubscriberImport', 'plgMigurAutomail');
		JFactory::getApplication()->registerEvent('onMigurAfterSubscriberAssign', 'plgMigurAutomail');
		JFactory::getApplication()->registerEvent('onMigurAfterUnsubscribe', 'plgMigurAutomail');
		JFactory::getApplication()->registerEvent('onMigurAfterSubscriberUnbind', 'plgMigurAutomail');
		JFactory::getApplication()->registerEvent('onMigurAfterSubscriberDelete', 'plgMigurAutomail');
		
		self::$_prepared = true;
	}

	
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
		@list($group) = explode('.', $group);
		$root = (!$native) ? JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $group : JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $group;
		$path = JPath::clean($root . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR . $plugin . '.xml');

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
				array(),//self::getNativeSupported(),
				self::getLocallySupported()
		);

		$items = array();
		for($i = 0; $i < count($extensions); $i++) {

			$item = $extensions[$i];
			// Add the info about module
			$xml = self::getInfo($item->extension, $item->native, $item->namespace);
			
			$extNamespace = !empty($xml->namespace)? (string)$xml->namespace : '';

			if (!self::namespaceCheckOccurence($namespace, $extNamespace)) {
				continue;
			}
			
			if (!isset($params['withoutInfo'])) {
				$item->xml = $xml;
			}

			if (empty($item->params)) {
				$item->params = "{}";
			}
			$item->params = (object) json_decode($item->params, true);

			$items[] = $item;
		}

		// If we are here then the necessary module could not found
		if (!empty($params['extension_id']) && !empty($params['native'])) {
			JError::raiseError(E_ERROR, "The module " . $params['extension_id'] . " could not found in the list of supported modules (native = " . $params['native'] . ")");
		}
		
		return $items;
	}

	
	/**
	 * Gets the list of ALL supported plugins
	 * 
	 * @return array - the list of supported modules 
	 */
	public function getItem($pid, $native = false)
	{
		if ($native) {
			$item = self::getNativeSupported($pid);
		} else {
			$item = self::getLocallySupported($pid);
		}

		$item = $item[0];
		$xml = self::getInfo($item->extension, $item->native, $item->namespace);

		if (!isset($params['withoutInfo'])) {
			$item->xml = $xml;
		}

		if (empty($item->params)) {
			$item->params = "{}";
		}
		$item->params = (object) json_decode($item->params, true);

		return $item;
	}
	
	/**
	 * Gets the list of supported local plugins.
	 * Gets the full info about each one.
	 *
	 * @return array - list of supported modules
	 * @since  1.0
	 */
	public function getLocallySupported($pid = null)
	{
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'extension_id, title, extension, params, namespace, '
			. $db->Quote(NewsletterTableNExtension::TYPE_PLUGIN) . ' AS type, '
			. '\'0\' AS native'
		);
		$query->from('`#__newsletter_extensions` AS a');

		// Filter by module
		$query->where('a.type = ' . $db->Quote(NewsletterTableNExtension::TYPE_PLUGIN));
		
		if ($pid > 0) {
			$query->where('a.extension_id = ' . (int) $pid);
		} 
		
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
	public function getNativeSupported($pid)
	{
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			"extension_id, `name` as title, element as extension, params, type, 'migur' AS namespace, ".
			"'1' AS native"
		);
		$query->from('`#__extensions` AS a');

		// Filter by module
		$query->where("a.type = 'plugin'");
		$query->where("a.folder = 'migur'");
		
		if ($pid > 0) {
			$query->where('a.extension_id = ' . (int) $pid);
		} 
		
		$query->order('a.name ASC');

		//echo nl2br(str_replace('#__','jos_',$query));
		return $db->setQuery($query)->loadObjectList();
	}

//	/**
//	 * Trigger the action of a plugin.
//	 *
//	 * @param	object	A module object.
//	 * @param	array	An array of attributes for the module (probably from the XML).
//	 *
//	 * @return	strign	The HTML content of the module output.
//	 * @since   1.0
//	 */
//	public static function trigger($pluginName, $group, $action, $params, $document)
//	{
//		self::getInstance($pluginName, $group);
//		return self::$_plugins[$group.'.'.$pluginName]->$action($params, $document);
//	}
//	
//	
//	/**
//	 * Trigger the action of a plugin.
//	 *
//	 * @param	object	A module object.
//	 * @param	array	An array of attributes for the module (probably from the XML).
//	 *
//	 * @return	strign	The HTML content of the module output.
//	 * @since   1.0
//	 */
//	public static function getInstance($pluginName, $group)
//	{
//		if (empty(self::$_plugins[$group.'.'.$pluginName])) {
//
//			require_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $pluginName, DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . $pluginName . '.php';
//			self::$_plugins[$group.'.'.$pluginName] = new $pluginName;
//			return self::$_plugins[$group.'.'.$pluginName];
//		}
//		
//		return null;
//	}
//
	
	/**
	 * Check if $requestedNamespace contain $extNamespace namespace.
	 * '',                     'newsletter.html' -> true
	 * 'newsletter',           'newsletter.html' -> true
	 * 'newsletter.html',      'newsletter.html' -> true
	 * 'newsletter.html.some', 'newsletter.html' -> false
	 * 'newsletter.plain',     'newsletter.html' -> false
	 * 
	 * @param type $requestedNamespace The namespace to check
	 * @param type $extNamespace The namespace that plugin have
	 * @return type 
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
			if (empty($ext[$i]) || $req[$i] != $ext[$i]) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * Loads all the plugin files for a particular type if no specific plugin is specified
	 * otherwise only the specific plugin is loaded.
	 *
	 * @param   string       $type        The plugin type, relates to the sub-directory in the plugins directory.
	 * @param   string       $plugin      The plugin name.
	 * @param   boolean      $autocreate  Autocreate the plugin.
	 * @param   JDispatcher  $dispatcher  Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public static function importPlugin($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		static $loaded = array();

		// check for the default args, if so we can optimise cheaply
		$defaults = false;
		if (is_null($plugin) && $autocreate == true && is_null($dispatcher))
		{
			$defaults = true;
		}

		if (!isset($loaded[$type]) || !$defaults)
		{
			$results = null;

			// Load the plugins from the database.
			$plugins = self::getLocallySupported();

			// Get the specified plugin(s).
			for ($i = 0, $t = count($plugins); $i < $t; $i++)
			{
				
				if (
					$plugins[$i]->type == 2 && 
					self::namespaceCheckOccurence($type, $plugins[$i]->namespace) && 
					($plugin == $plugins[$i]->extension || $plugin == null)
				) {
					self::_import($plugins[$i], $autocreate, $dispatcher);
					$results = true;
				}
			}

			// Bail out early if we're not using default args
			if (!$defaults)
			{
				return $results;
			}
			$loaded[$type] = $results;
		}

		return $loaded[$type];
	}


	/**
	 * Add into dispatcher the collection of a plugins. 
	 * Collection item contain DB data of a plugin.
	 *
	 * @param   string       $plugins     List of objects
	 * @param   JDispatcher  $dispatcher  Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public static function importPluginCollection($plugins, $dispatcher = null)
	{
		/*static*/ $loaded = array();
		
		if (!is_array($plugins)) {
			throw new Exception('Collection of plugins is not array');
		}

		// Get the specified plugin(s).
		for ($i = 0, $t = count($plugins); $i < $t; $i++)
		{
			$pid = $plugins[$i]->namespace . '.' . $plugins[$i]->extension;

			if (!isset($loaded[$pid]))
			{
				self::_import($plugins[$i], true, $dispatcher);
				$loaded[$pid] = true;
			}	
		}

		return $loaded;
	}
	
	
	/**
	 * Loads the plugin file.
	 *
	 * @param   JPlugin      &$plugin     The plugin.
	 * @param   boolean      $autocreate  True to autocreate.
	 * @param   JDispatcher  $dispatcher  Optionally allows the plugin to use a different dispatcher.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	protected static function _import(&$plugin, $autocreate = true, $dispatcher = null)
	{
		/*static*/ $paths = array();

		@list($group) = explode('.', $plugin->namespace);
		
		$plugin->extension = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->extension);

		$path = MigurPluginHelper::getFolder($plugin->extension, $group) . DIRECTORY_SEPARATOR . $plugin->extension . '.php';

		if (!isset($paths[$path]))
		{
			$pathExists = file_exists($path);
			if ($pathExists)
			{
				if (!isset($paths[$path]))
				{
					require_once $path;
				}
				$paths[$path] = true;

				if ($autocreate)
				{
					// Makes sure we have an event dispatcher
					if (!is_object($dispatcher))
					{
						$dispatcher = NewsletterPluginManager::getInstance();
					}

					$className = 'plg' . ucfirst($group) . ucfirst($plugin->extension);
					if (class_exists($className))
					{
                        self::_loadLang($plugin->extension, $group);
                                            
						// Load the plugin from the database.
						if (!isset($plugin->params))
						{
							// Seems like this could just go bye bye completely
							$plugin = self::getPlugin($group, $plugin->extension);
						}

						// Instantiate and register the plugin.
						$plugin->name = $plugin->extension;
						$plugin->type = $group;
						$plugin->params = new JRegistry($plugin->params);
						new $className($dispatcher, (array) ($plugin));
					}
				}
			}
			else
			{
				$paths[$path] = false;
			}
		}
	}
        
        static function _loadLang($name, $group)
        {
            if (!self::$_lang instanceof JLanguage) {
                self::$_lang = JFactory::getLanguage();
            }

            $path = 
                JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR .
                'extensions' . DIRECTORY_SEPARATOR .
                'plugins' . DIRECTORY_SEPARATOR .
                $group . DIRECTORY_SEPARATOR .
                $name;

            
            self::$_lang->load($name, $path);
        }
		
		
		static function getFolder($extension, $namespace)
		{
			@list($group) = explode('.', $namespace);
			
			return JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 
			'components' . DIRECTORY_SEPARATOR . 
			'com_newsletter' . DIRECTORY_SEPARATOR . 
			'extensions' . DIRECTORY_SEPARATOR . 
			'plugins' . DIRECTORY_SEPARATOR . 
			$group . DIRECTORY_SEPARATOR . 
			$extension;
		}	
}

/**
 * Legacy support for class name
 */
abstract class MigurPluginHelper extends NewsletterHelperPlugin 
{}