<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.installer.installer');

/**
 * Joomla base installer class
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class NewslettterClassExtensionInstaller extends JInstaller
{
	/**
	 * Constructor
	 *
	 * @since   11.1
	 */
	public function __construct($basepath = null, $classprefix = 'NewsletterClassExtensionAdapter', $adapterfolder = 'adapter', $options = array())
	{
		parent::__construct();
		
		$this->_basepath = !empty($basepath)? $basepath : dirname(__FILE__);
		$this->_classprefix = $classprefix;
		$this->_adapterfolder = $adapterfolder;
		
		if (!empty($options['db']) && $options['db'] instanceof JDatabaseDriver) {
			$this->_db = $options['db'];
		}	
	}
	

	/**
	 * Loads all adapters.
	 *
	 * @param   array  $options  Adapter options
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function loadAllAdapters($options = array())
	{
		$list = JFolder::files($this->_basepath . '/' . $this->_adapterfolder);

		foreach ($list as $filename)
		{
			if (JFile::getExt($filename) == 'php')
			{
				// Try to load the adapter object
				require_once $this->_basepath . '/' . $this->_adapterfolder . '/' . $filename;

				$name = JFile::stripExt($filename);
				$class = $this->_classprefix . ucfirst($name);

				if (!class_exists($class))
				{
					// Skip to next one
					continue;
				}

				$adapter = new $class($this, $this->_db, $options);
				
				if(!empty($adapter->adapterName)) {
					$name = $adapter->adapterName;
				}
				$this->_adapters[$name] = clone $adapter;
			}
		}
	}
}

