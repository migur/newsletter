<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.base.adapterinstance');

/**
 * Module installer
 *
 * @package     Joomla.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class NewsletterClassExtensionAdapterTemplate extends JAdapterInstance
{
	
	/**
	 * Install function routing
	 *
	 * @var    string
	 * @since 11.1
	 */
	protected $route = 'Install';

	public    $adapterName = 'newsletter_template';
	/**
	 * @var
	 * @since 11.1
	 */
	protected $manifest = null;

	/**
	 * @var
	 * @since 11.1
	 */
	protected $manifest_script = null;

	/**
	 * Extension name
	 *
	 * @var
	 * @since   11.1
	 */
	protected $name = null;

	/**
	 * @var
	 * @since  11.1
	 */
	protected $element = null;

	/**
	 * @var    string
	 * @since 11.1
	 */
	protected $scriptElement = null;

	protected $_extPath = '';
	
	
	public function __construct(&$parent, &$db, $options = array()) {
		
		parent::__construct($parent, $db, $options);
		
		$this->_extPath = 
			!empty($options['extPath'])? $options['extPath'] : 
			
			JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 
			'components' . DIRECTORY_SEPARATOR . 
			'com_newsletter' . DIRECTORY_SEPARATOR . 
			'extensions' . DIRECTORY_SEPARATOR . 
			'templates';
	}
	

	/**
	 * Custom install method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function install()
	{
		// Get a database connector object
		$db = $this->parent->getDbo();

		// Get the extension manifest object
		$this->manifest = $this->parent->getManifest();

		// Manifest Document Setup Section

		// Set the extensions name
		$name = (string) $this->manifest->information->name;
		$name = JFilterInput::getInstance()->clean($name, 'string');
		$this->set('name', $name);

		// Get the component description
		$description = (string) $this->manifest->information->description;
		if ($description)
		{
			$this->parent->set('message', JText::_($description));
		}
		else
		{
			$this->parent->set('message', '');
		}

		// No client attribute was found so we assume the site as the client
		$cname = 'admin';
		$clientId = 1;

		$element = (string) $this->manifest->information->template;
		$this->set('element', $element);
		
		if (!empty($element))
		{
			$this->parent->setPath('extension_root', $this->_extPath);
		}
		else
		{
			$this->parent->abort(JText::sprintf('JLIB_INSTALLER_ABORT_MOD_INSTALL_NOFILE', JText::_('JLIB_INSTALLER_' . $this->route)));

			return false;
		}

		// Check to see if a module by the same name is already installed
		// If it is, then update the table because if the files aren't there
		// we can assume that it was (badly) uninstalled
		// If it isn't, add an entry to extensions
		$query = $db->getQuery(true);
		$query->select($query->qn('extension_id'))->from($query->qn('#__newsletter_extensions'));
		$query
			->where($query->qn('extension') . ' = ' . $query->q($element))
			->where($query->qn('type') . ' = ' . 3);
		$db->setQuery($query);

		try
		{
			$db->Query();
		}
		catch (JException $e)
		{
			// Install failed, roll back changes
			$this->parent
				->abort(JText::sprintf('JLIB_INSTALLER_ABORT_MOD_ROLLBACK', JText::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true)));

			return false;
		}

		$id = $db->loadResult();

		// Since we created the module directory and will want to remove it if
		// we have to roll back the installation, let's add it to the
		// installation step stack


		// Database Processing Section
		$row = JTable::getInstance('NExtension', 'NewsletterTable');

		// Was there a module already installed with the same name?
		if ($id)
		{
			// Load the entry and update the manifest_cache
			$row->load($id);
			$row->title = $this->get('name'); // update name
			$row->set('params', $this->parent->getParams());

			if (!$row->store())
			{
				// Install failed, roll back changes
				$this->parent
					->abort(JText::sprintf('JLIB_INSTALLER_ABORT_MOD_ROLLBACK', JText::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true)));

				return false;
			}
		}
		else
		{
			$row->set('title', $this->get('name'));
			$row->set('extension', $this->get('element'));
			$row->set('type', '3');
			$row->set('params', $this->parent->getParams());

			if (!$row->store())
			{
				// Install failed, roll back changes
				$this->parent
					->abort(JText::sprintf('JLIB_INSTALLER_ABORT_MOD_ROLLBACK', JText::_('JLIB_INSTALLER_' . $this->route), $db->stderr(true)));
				return false;
			}

			// Set the insert id
			$row->extension_id = $db->insertid();

			// Since we have created a module item, we add it to the installation step stack
			// so that if we have to rollback the changes we can undo it.
			$this->parent->pushStep(array('type' => 'extension', 'extension_id' => $row->extension_id));
		}



		// Get the client info
		$path = array();
		$path['src'] = $this->parent->getPath('manifest');
		$path['dest'] = $this->parent->getPath('extension_root') . '/' . basename($this->parent->getPath('manifest'));

		if (!$this->parent->copyFiles(array($path), true))
		{
			// Install failed, rollback changes
			$this->parent->abort(JText::_('JLIB_INSTALLER_ABORT_MOD_INSTALL_COPY_SETUP'));

			return false;
		}

		return $row->get('extension_id');
	}

	/**
	 * Custom update method
	 *
	 * This is really a shell for the install system
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function update()
	{
		// Set the overwrite setting
		$this->parent->setOverwrite(true);
		$this->parent->setUpgrade(true);
		// Set the route for the install
		$this->route = 'Update';

		// Go to install which handles updates properly
		return $this->install();
	}


	/**
	 * Refreshes the extension table cache
	 *
	 * @return  boolean  Result of operation, true if updated, false on failure.
	 *
	 * @since   11.1
	 */
	public function refreshManifestCache()
	{
		$client = JApplicationHelper::getClientInfo($this->parent->extension->client_id);
		$manifestPath = $client->path . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->parent->extension->extension . '/' . $this->parent->extension->extension . '.xml';
		$this->parent->manifest = $this->parent->isManifest($manifestPath);
		$this->parent->setPath('manifest', $manifestPath);
		$manifest_details = JApplicationHelper::parseXMLInstallFile($this->parent->getPath('manifest'));
		$this->parent->extension->manifest_cache = json_encode($manifest_details);
		$this->parent->extension->name = $manifest_details['name'];

		if ($this->parent->extension->store())
		{
			return true;
		}
		else
		{
			JError::raiseWarning(101, JText::_('JLIB_INSTALLER_ERROR_MOD_REFRESH_MANIFEST_CACHE'));

			return false;
		}
	}

	/**
	 * Custom uninstall method
	 *
	 * @param   integer  $id  The id of the module to uninstall
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	public function uninstall($id)
	{
		// Initialise variables.
		$row = null;
		$retval = true;
		$db = $this->parent->getDbo();

		// First order of business will be to load the module object table from the database.
		// This should give us the necessary information to proceed.
		$row = JTable::getInstance('NExtension', 'NewsletterTable');

		if (!$row->load((int) $id) || !strlen($row->extension))
		{
			JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_ERRORUNKOWNEXTENSION'));
			return false;
		}

		// Get the extension root path
		$element = $row->extension;
		$client = 1;

		$this->parent->setPath('extension_root', $this->_extPath . DIRECTORY_SEPARATOR . $row->extension . ".xml");

		// Wipe out any instances in the modules table
		$query = 'DELETE' . ' FROM #__newsletter_template_styles' . ' WHERE template=' . $db->Quote($row->extension.'.xml');
		$db->setQuery($query);
		try
		{
			$db->query();
		}
		catch (JException $e)
		{
			JError::raiseWarning(100, JText::sprintf('JLIB_INSTALLER_ERROR_MOD_UNINSTALL_EXCEPTION', $db->stderr(true)));
			$retval = false;
		}

		// Now we will no longer need the module object, so let's delete it and free up memory
		$query = 'DELETE FROM #__newsletter_extensions WHERE extension_id = ' . $db->Quote($row->extension_id);
		$db->setQuery($query);
		try
		{
			// Clean up any other ones that might exist as well
			$db->Query();
		}
		catch (JException $e)
		{
			// Ignore the error...
		}

		unset($row);
		
		// Remove the installation folder
		if (!JFile::delete($this->parent->getPath('extension_root')))
		{
			// JFolder should raise an error
			$retval = false;
		}

		return $retval;
	}


	/**
	 * Custom discover method
	 *
	 * @return  array  JExtension list of extensions available
	 *
	 * @since   11.1
	 */
	public function discover()
	{
		$results = array();
		$admin_list = JFolder::files($this->_extPath, '\.xml');
		$admin_info = JApplicationHelper::getClientInfo('administrator', true);

		foreach ($admin_list as $tpl)
		{
			if ($xml = $this->parent->isManifest("$this->_extPath/$tpl")) {
				
				$extension = JTable::getInstance('NExtension', 'NewsletterTable');
				$extension->set('title', (string) $xml->information->name);
				$extension->set('extension', str_replace('.xml', '', $tpl));
				$extension->set('params', '{}');
				$extension->set('type', '3');
				$extension->set('namespace', '');
				$results[] = clone $extension;
			}	
		}

		return $results;
	}
}
