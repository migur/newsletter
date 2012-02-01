<?php

/**
 * The newsletter module helper
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

// Import library dependencies
jimport('joomla.application.module.helper');
JLoader::import('tables.nextension', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Module helper class
 *
 * @static
 * @since		1.0
 */
abstract class MigurModuleHelper extends JModuleHelper
{

	public static $clean;
	public static $itemId;
	public static $defaults = array(
		'author' => 'COM_NEWSLETTER_UNKNOWN',
		'name' => 'COM_NEWSLETTER_UNKNOWN'
	);

	/**
	 * Gets the data from module config file
	 *
	 * @param <type> $module - module identificator (mod_*)
	 * @param <type> $namespace - 'joomla' or 'component'
	 *
	 * @return JObject - data
	 * @since 1.0
	 */
	public static function getInfo($module, $native = false)
	{
		$root = (!$native) ? JPATH_COMPONENT_ADMINISTRATOR . DS . 'extensions' . DS . 'modules' : JPATH_SITE . DS . 'modules';
		$path = JPath::clean($root . DS . $module . DS . $module . '.xml');
		if (file_exists($path)) {
			$xml = simplexml_load_file($path);
		} else {
			$xml = new JObject();
		}

		/* Set the default values */
		foreach (self::$defaults as $name => $item) {
			if (!isset($xml->{$name})) {
				$xml->{$name} = JText::_($item);
			}
		}

		return $xml;
	}

	/**
	 * Render the module.
	 *
	 * @param	object	A module object.
	 * @param	array	An array of attributes for the module (probably from the XML).
	 *
	 * @return	strign	The HTML content of the module output.
	 * @since   1.0
	 */
	public static function renderModule($module, $attribs = array())
	{
		static $chrome;

		$option = JRequest::getCmd('option');
		$app = JFactory::getApplication();

		// Record the scope.
		$scope = $app->scope;

		// Set scope to component name
		$app->scope = $module->module;

		// Get module parameters
		$params = new JRegistry;
		if (is_string($module->params)) {
			$params->loadJSON($module->params);
		} else {
			$params->loadObject((object) $module->params);
		}

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);

		if ($module->native == 0) {
			$path = JPATH_COMPONENT_ADMINISTRATOR . '/extensions/modules/' . $module->module . '/' . $module->module . '.php';
		} else {
			$path = JPATH_SITE . '/modules/' . $module->module . '/' . $module->module . '.php';
		}

		// Load the module
		if (empty($module->user) && file_exists($path)) {
			$lang = JFactory::getLanguage();
			// 1.5 or Core then
			// 1.6 3PD
			$lang->load($module->module, JPATH_BASE, null, false, false)
				|| $lang->load($module->module, dirname($path), null, false, false)
				|| $lang->load($module->module, JPATH_BASE, $lang->getDefault(), false, false)
				|| $lang->load($module->module, dirname($path), $lang->getDefault(), false, false);

			$content = '';

			// Emulation of SITE
			$app = JFactory::$application;

			try {

				JFactory::$application = null;
				JFactory::getApplication('site');
				ob_start();
				require $path;
				$module->content = ob_get_contents() . $content;
				ob_end_clean();
			} catch (Exception $e) {

			}

			JFactory::$application = $app;
		}

		// Load the module chrome functions
		if (!$chrome) {
			$chrome = array();
		}

		require_once JPATH_THEMES . '/system/html/modules.php';
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/modules.php';
		if (!isset($chrome[$chromePath])) {
			if (file_exists($chromePath)) {
				require_once $chromePath;
			}
			$chrome[$chromePath] = true;
		}

		//make sure a style is set
		if (!isset($attribs['style'])) {
			$attribs['style'] = 'newsletterDefault';
		}



		//dynamically add outline style
		if (JRequest::getBool('tp') && JComponentHelper::getParams('com_templates')->get('template_positions_display')) {
			$attribs['style'] .= ' outline';
		}


		foreach (explode(' ', $attribs['style']) as $style) {
			$chromeMethod = 'modChrome_' . $style;

			// Apply chrome and render module
			if (function_exists($chromeMethod)) {
				$module->style = $attribs['style'];

				ob_start();
				$chromeMethod($module, $params, $attribs);
				$module->content = ob_get_contents();
				ob_end_clean();
			}
		}

		$app->scope = $scope; //revert the scope

		return $module->content;
	}

	/**
	 * Load all the modules used in the newsletter
	 *
	 * @return array
	 * @since  1.0
	 */
	protected static function &_load()
	{
		if (!empty(self::$clean)) {
			return self::$clean;
		}

		if (self::$itemId < 1) {
			self::$clean = array();
			return self::$clean;
		}

		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select(
			'newsletters_ext_id as id, ne.title, extension as module, position, NULL as content, ne.showtitle, ne.params as params, e.params as params_default, NULL as menuid, 0 as native');
		$query->from('#__newsletter_extensions AS e');
		$query->join('', '#__newsletter_newsletters_ext AS ne ON e.extension_id = ne.extension_id');
		$query->where('type = 1');
		$query->where('ne.newsletter_id = "' . self::$itemId . '"');
		$query->where('ne.native = 0');

		// Set the query
		$db->setQuery($query);
		$modulesCom = $db->loadObjectList();

		$query = $db->getQuery(true);
		$query->select(
			'newsletters_ext_id AS id, ne.title, element AS module, position, '
			. 'NULL AS content, ne.showtitle, ne.params AS params, '
			. 'e.params AS params_default, NULL AS menuid, 1 AS native'
		);
		$query->from('#__extensions AS e');
		$query->join('', '#__newsletter_newsletters_ext AS ne ON e.extension_id = ne.extension_id');
		$query->where('e.type = "module"');
		$query->where('ne.newsletter_id = "' . self::$itemId . '"');
		$query->where('ne.native = 1');

		// Set the query
		$db->setQuery($query);
		$modulesNat = $db->loadObjectList();

		$modules = array_merge($modulesCom, $modulesNat);

		self::$clean = array();

		if ($db->getErrorNum()) {
			JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
			return self::$clean;
		}

		// Apply negative selections and eliminate duplicates
		$negId = self::$itemId ? -(int) self::$itemId : false;
		$dupes = array();
		for ($i = 0, $n = count($modules); $i < $n; $i++) {
			$module = &$modules[$i];
			//determine if this is a custom module
			$file = $module->module;
			$custom = substr($file, 0, 4) == 'mod_' ? 0 : 1;
			$module->user = $custom;
			// Custom module name is given by the title field, otherwise strip off "com_"
			$module->name = $custom ? $module->title : substr($file, 4);
			$module->style = null;
			$module->position = strtolower($module->position);

			if (empty($module->params)) {
				$module->params = $module->params_default;
			}

			self::$clean[$module->id] = $module;
		}
		// Return to simple indexing that matches the query order.
		self::$clean = array_values(self::$clean);
		return self::$clean;
	}

	/**
	 * Get module by name (real, eg 'Breadcrumbs' or folder, eg 'mod_breadcrumbs')
	 *
	 * @param	string	The name of the module
	 * @param	string	The title of the module, optional
	 *
	 * @return	object	The Module object
	 */
	public static function getModule($name, $title = null)
	{
		$result = null;
		$modules = self::_load();
		$total = count($modules);
		
		for ($i = 0; $i < $total; $i++) {
			// Match the name of the module
			if ($modules[$i]->name == $name) {
				// Match the title if we're looking for a specific instance of the module
				if (!$title || $modules[$i]->title == $title) {
					$result = &$modules[$i];
					break; // Found it
				}
			}
		}

		// if we didn't find it, and the name is mod_something, create a dummy object
		if (is_null($result) && substr($name, 0, 4) == 'mod_') {
			$result = new stdClass;
			$result->id = 0;
			$result->title = '';
			$result->module = $name;
			$result->position = '';
			$result->content = '';
			$result->showtitle = 0;
			$result->control = '';
			$result->params = '';
			$result->user = 0;
		}

		return $result;
	}

	/**
	 * Get all the modules used in newsletter for specified position.
	 *
	 * @param string	$position	The position of the module
	 *
	 * @return array	An array of module objects
	 */
	public static function &getModules($position)
	{
		$position = strtolower($position);
		$result = array();

		$modules = MigurModuleHelper::_load();

		$total = count($modules);
		for ($i = 0; $i < $total; $i++) {
			if ($modules[$i]->position == $position) {
				$result[] = &$modules[$i];
			}
		}
		return $result;
	}

	/**
	 * Gets the list of ALL supported modules
	 * Or the array with one necessary module if parameters is present
	 *
	 * @param  array - array with the extension_id AND native flag
	 *
	 * @return array - the list of supported modules
	 * @since  1.0
	 */
	public static function getSupported($params = array())
	{
		$extensions = array_merge(
				self::getNativeSupported(),
				self::getLocallySupported()
		);

		foreach ($extensions as &$item) {

			// Add the info about module
			if (!isset($params['withoutInfo'])) {
				$item->xml = MigurModuleHelper::getInfo($item->extension, $item->native);
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
	 * Gets the list of supported local modules (not plugins).
	 * Gets the full info about each one.
	 *
	 * @return array - list of supported modules
	 * @since  1.0
	 */
	public static function getLocallySupported()
	{
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'extension_id, title, extension, params, '
			. 'type, 0 AS native');
		$query->from('`#__newsletter_extensions` AS a');

		// Filter by module
		$query->where('a.type = ' . $db->Quote(NewsletterTableNExtension::TYPE_MODULE));
		$query->order('a.title ASC');

		//echo nl2br(str_replace('#__','jos_',$query));
		$modules = $db->setQuery($query)->loadObjectList();
		return JArrayHelper::sortObjects($modules, 'title', 1, false);
	}

	/**
	 * Gets the list of supported native modules (not plugins).
	 * Only for frontend.
	 * Gets the full info about each one.
	 *
	 * @return array - list of supported modules
	 * @since  1.0
	 */
	public static function getNativeSupported()
	{
		// Get list of native supported modules
		$array = self::getNativeSupportedNames();

		$sqlIn = "'" . implode('\',\'', $array) . "'";
		
		// Fetch it
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'extension_id, name as title, element as extension, params, '
			. $db->Quote(NewsletterTableNExtension::TYPE_MODULE) . ' AS type, '
			. '\'1\' AS native'
		);
		$query->from('`#__extensions` AS a');

		// Filter by module
		$query->where('a.type = ' . $db->Quote('module'));

		// Only for frontend
		$query->where('a.client_id = 0');
		$query->where('a.name in (' . $sqlIn . ')');

		$query->order('a.name ASC');

		//echo nl2br(str_replace('#__','jos_',$query));
		$modules = $db->setQuery($query)->loadObjectList();


		// Initialise variables.
		$client = JApplicationHelper::getClientInfo(0);
		$lang = JFactory::getLanguage();

		foreach ($modules as $item) {
			// 1.5 Format; Core files or language packs then
			// 1.6 3PD Extension Support
			$lang->load($item->extension . '.sys', $client->path, null, false, false)
				|| $lang->load($item->extension . '.sys', $client->path . '/modules/' . $item->extension, null, false, false)
				|| $lang->load($item->extension . '.sys', $client->path, $lang->getDefault(), false, false)
				|| $lang->load($item->extension . '.sys', $client->path . '/modules/' . $item->extension, $lang->getDefault(), false, false);
			$item->title = JText::_($item->title);
			if (isset($item->xml) && $text = trim($item->xml->description)) {
				$item->desc = JText::_($text);
			} else {
				$item->desc = JText::_('COM_MODULES_NODESCRIPTION');
			}
		}

		return JArrayHelper::sortObjects($modules, 'title', 1, false);
	}

	/**
	 * Parses the XML with list of supported modules and returns
	 * the list of the names of each one.
	 *
	 * @return array - the manes of supported native modules
	 * @since  1.0
	 */
	public static function getNativeSupportedNames()
	{
		$file = realpath(JPATH_COMPONENT_ADMINISTRATOR) . DS . 'modules.xml';

		// Attempt to load the xml file.
		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			$children = $xml->children();
		} else {
			JError::raiseError(E_ERROR, "The file " . $file . " not found");
		}

		$seen = array();

		if (count($children) > 0) {

			foreach ($children as $child => $value) {
				$childname = $value['name'];
				if (!in_array($childname, $seen)) {
					array_push($seen, $childname);
				}
			}
		}

		return $seen;
	}

}

/**
 * From beezDivision chrome.
 * Wrapper for module
 * 
 * @since	1.0
 */
function modChrome_newsletterDefault($module, &$params, &$attribs)
{
	if (!empty($module->content)) {

		echo '<div>';
		if ($module->showtitle) {
			echo "<h1><span> $module->title </span></h1>";
		}
		echo $module->content;
		echo '</div>';
	}
}
