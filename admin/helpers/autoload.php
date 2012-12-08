<?php

class NewsletterHelperAutoload
{

	public static $prefixes;
	
	public static $componentAdminPath;

	/**
	 * Method to setup the autoloaders for the Joomla Platform.  Since the SPL autoloaders are
	 * called in a queue we will add our explicit, class-registration based loader first, then
	 * fall back on the autoloader based on conventions.  This will allow people to register a
	 * class in a specific location and override platform libraries as was previously possible.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	public static function setup()
	{
		$componentPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_newsletter';
		self::$componentAdminPath = $componentPath;
		$migurlibPath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'migur' . DIRECTORY_SEPARATOR . 'library';

		// Register the base path for Joomla platform libraries.
		jimport('migur.migur');
		self::registerPrefix('MigurJ', $migurlibPath);
		self::registerPrefix('Migur', $migurlibPath);
		self::registerPrefix('NewsletterAutomlailingPlan', $componentPath . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'automailing' . DIRECTORY_SEPARATOR . 'plans');
		self::registerPrefix('NewsletterAutomlailingThread', $componentPath . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'automailing' . DIRECTORY_SEPARATOR . 'threads');
		
		self::registerPrefix('NewsletterHelper', $componentPath . DIRECTORY_SEPARATOR . 'helpers');
		self::registerPrefix('NewsletterModelEntity',  $componentPath . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'entities');
		self::registerPrefix('NewsletterModel',  $componentPath . DIRECTORY_SEPARATOR . 'models');
		self::registerPrefix('NewsletterTable',  $componentPath . DIRECTORY_SEPARATOR . 'tables');

		JLoader::import('helpers.module', $componentPath);
		
		// Register the autoloader functions.
		spl_autoload_register(array('NewsletterHelperAutoload', '_autoload'));
	}

	/**
	 * Register a class prefix with lookup path.  This will allow developers to register library
	 * packages with different class prefixes to the system autoloader.  More than one lookup path
	 * may be registered for the same class prefix, but if this method is called with the reset flag
	 * set to true then any registered lookups for the given prefix will be overwritten with the current
	 * lookup path.
	 *
	 * @param   string   $prefix  The class prefix to register.
	 * @param   string   $path    Absolute file path to the library root where classes with the given prefix can be found.
	 * @param   boolean  $reset   True to reset the prefix with only the given lookup path.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public static function registerPrefix($prefix, $path, $reset = false)
	{
		// Verify the library path exists.
		if (!file_exists($path)) {
			throw new Exception('Path ' . $path . ' cannot be found.', 500);
		}

		// If the prefix is not yet registered or we have an explicit reset flag then set set the path.
		if (!isset(self::$prefixes[$prefix]) || $reset) {
			self::$prefixes[$prefix] = array($path);
		}
		// Otherwise we want to simply add the path to the prefix.
		else {
			self::$prefixes[$prefix][] = $path;
		}
	}

	/**
	 * Autoload a class based on name.
	 *
	 * @param   string  $class  The class to be loaded.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 */
	private static function _autoload($class)
	{
		// BAD HALPERS NAMING SUPPORT!!
		if (strrpos($class, 'Helper') == strlen($class) - strlen('Helper')) {

			$file = self::$componentAdminPath . 
				DIRECTORY_SEPARATOR . 'helpers' . 
				DIRECTORY_SEPARATOR . strtolower(str_replace('Helper', '', $class)) . '.php';

			if (file_exists($file)) {
				include_once $file;
			}	
		}
		//
		
		foreach (self::$prefixes as $prefix => $lookup) {
			if (strpos($class, $prefix) === 0) {
				return self::_load(substr($class, strlen($prefix)), $lookup);
			}
		}
	}

	/**
	 * Load a class based on name and lookup array.
	 *
	 * @param   string  $class   The class to be loaded (wihtout prefix).
	 * @param   array   $lookup  The array of base paths to use for finding the class file.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	private static function _load($class, $lookup)
	{
		// Split the class name into parts separated by camelCase.
		$parts = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', $class);

		// If there is only one part we want to duplicate that part for generating the path.
		//$parts = (count($parts) === 1) ? array($parts[0], $parts[0]) : $parts;

		foreach ($lookup as $base) {
			// Generate the path based on the class name parts.
			$path = $base . '/' . implode('/', array_map('strtolower', $parts)) . '.php';

			// Load the file if it exists.
			if (file_exists($path)) {
				return include_once $path;
			}
		}
	}

}
