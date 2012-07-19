<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_newsletter
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies

jimport('joomla.application.component.model');

/**
 * Extension Manager Install Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_newsletter
 * @since		1.5
 */
class NewsletterModelInstall extends MigurModelList
{
	/**
	 * @var object JTable object
	 */
	protected $_table = null;

	/**
	 * @var object JTable object
	 */
	protected $_url = null;

	/**
	 * @var object JInstaller instance
	 */
	public $installer = null;

	protected $_context = 'com_newsletter.install';
	
	/**
	 * The constructor of a class
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'extension_id', 'a.extension_id',
				'title', 'a.title',
				'extension', 'a.extension',
				'type', 'a.type'
			);
		}

		parent::__construct($config);
	}
	
	
	
	/**
	 * Install an extension from either folder, url or upload.
	 *
	 * @return	boolean result of install
	 * @since	1.5
	 */
	function install()
	{
		$this->setState('action', 'install');

		$app = JFactory::getApplication();
		
		// Remember the 'Install from Directory' path.
		$app->getUserStateFromRequest($this->_context.'.install_directory', 'install_directory');
		$package = $this->_getPackageFromUpload();

		// Was the package unpacked?
		if (!$package) {
			$app->setUserState('com_newsletter.message', JText::_('COM_NEWSLETTER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}

		
		$installer = $this->getInstaller();

		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('COM_NEWSLETTER_INSTALL_ERROR', JText::_('COM_NEWSLETTER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('COM_NEWSLETTER_INSTALL_SUCCESS', JText::_('COM_NEWSLETTER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = true;
		}

		// Set some model state values
		$app	= JFactory::getApplication();
		$app->enqueueMessage($msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$app->setUserState('
			com_newsletter.message', $installer->message);
		$app->setUserState('com_newsletter.extension_message', $installer->get('extension_message'));
		$app->setUserState('com_newsletter.redirect_url', $installer->get('redirect_url'));

		// Cleanup the install files
		if (!is_file($package['packagefile'])) {
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);


		return $result;
	}

		
	
	/**
	 * Remove (uninstall) an extension
	 *
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function remove($eid = array())
	{
		// Initialise variables.
		$user = JFactory::getUser();
		if ($user->authorise('core.delete', 'com_newsletter')) {

			// Initialise variables.
			$failed = array();

			/*
			* Ensure eid is an array of extension ids in the form id => client_id
			* TODO: If it isn't an array do we want to set an error and fail?
			*/
			if (!is_array($eid)) {
				$eid = array($eid => 0);
			}

			// Get a database connector
			$db = JFactory::getDBO();

			// Get an installer object for the extension type
			$installer = $this->getInstaller();
			$row = JTable::getInstance('NExtension', 'NewsletterTable');

			// Uninstall the chosen extensions
			foreach($eid as $id) {
				$id = trim($id);
				$row->load($id);
				if ($row->type) {
					
					$type = $this->getStringType($row->type);
					$result = $installer->uninstall($type, $id);

					// Build an array of extensions that failed to uninstall
					if ($result === false) {
						$failed[] = $id;
					}
				}
				else {
					$failed[] = $id;
				}
			}

			$langstring = 'COM_NEWSLETTER_EXTENSION_TYPE_'. strtoupper($row->extension);
			$rowtype = JText::_($langstring);
			if(strpos($rowtype, $langstring) !== false) {
				$rowtype = $row->extension;
			}

			if (count($failed)) {

				// There was an error in uninstalling the package
				$msg = JText::sprintf('COM_NEWSLETTER_UNINSTALL_ERROR', $rowtype);
				$result = false;
			} else {

				// Package uninstalled sucessfully
				$msg = JText::sprintf('COM_NEWSLETTER_UNINSTALL_SUCCESS', $rowtype);
				$result = true;
			}
			$app = JFactory::getApplication();
			$app->enqueueMessage($msg);
			$this->setState('action', 'remove');
			$this->setState('name', $installer->get('name'));
			$app->setUserState('com_newsletter.message', $installer->message);
			$app->setUserState('com_newsletter.extension_message', $installer->get('extension_message'));
			return $result;
		} else {
			$result = false;
			JError::raiseWarning(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}
	}
	
	
	public function getInstaller()
	{
		
		if (!$this->installer) {
			JLoader::import('class.extension.installer', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_newsletter');
			$this->installer = new NewslettterClassExtensionInstaller();
			$this->installer->loadAllAdapters();
		}
		
		return $this->installer;
	}

	public function getStringType($typeId) 
	{
		switch($typeId) {
			case 1: return 'newsletter_module';
			case 2: return 'newsletter_plugin';
			case 3: return 'newsletter_template';
		}
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		$this->setState('message', $app->getUserState('com_newsletter.message'));
		$this->setState('extension_message', $app->getUserState('com_newsletter.extension_message'));
		$app->setUserState('com_newsletter.message', '');
		$app->setUserState('com_newsletter.extension_message', '');

		// Recall the 'Install from Directory' path.
		$path = $app->getUserStateFromRequest($this->_context.'.install_directory', 'install_directory', $app->getCfg('tmp_path'));
		$this->setState('install.directory', $path);
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.0
	 */
	public function setDefaultQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// SQL-query for gettting the users-subscibers list.
		$query->select('a.*');
		$query->from('#__newsletter_extensions AS a');
		
		// Filtering the data
		if (!empty($this->filtering)) {
			foreach ($this->filtering as $field => $val)
				$query->where($field . '=' . $val);
		}
		unset($this->filtering);

		// Filter by list state
		$type = $this->getState('filter.type');
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query)); die;
		$this->query = $query;
	}
	
	
	/**
	 * Works out an installation package from a HTTP upload
	 *
	 * @return package definition or false on failure
	 */
	protected function _getPackageFromUpload()
	{
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_NEWSLETTER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_NEWSLETTER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile)) {
			JError::raiseWarning('', JText::_('COM_NEWSLETTER_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			JError::raiseWarning('', JText::_('COM_NEWSLETTER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path') . '/' . $userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest);

		return $package;
	}

	public function restore()
	{
		$installer = $this->getInstaller();
		
		$extensions = (array) $installer->discover();
		
		// Load all registered extensions first
		$db = JFactory::getDbo();
		$db->setQuery('SELECT * FROM #__newsletter_extensions');
		$dbExts = $db->loadObjectList();
		
		$processed = array();
		
		foreach ($extensions as $item) {
			
			// Trying to load record...
			$table = JTable::getInstance('NExtension', 'NewsletterTable');
			$table->load(array(
					'extension' => $item->extension,
					'type' => $item->type,
					'namespace' => $item->namespace
			));
			
			// ...and then refresh anyway!
			$table->save((array) $item);
			
			// At least mark this extension as processed.
			$processed[] = (int) $table->extension_id;
			
			unset($table);
		}

		// Now let's remove all unprocessed (absent) extensions...
		foreach($dbExts as $item) {
			
			if (!in_array($item->extension_id, $processed)) {
				
				// This trash need to remove
				$table = JTable::getInstance('NExtension', 'NewsletterTable');
				$table->delete($item->extension_id);
				unset($table);
			}
		}
	}
}
