<?php

/**
 * The extension model. Implements the standard functional for extension view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('migur.library.mailer.document');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Class of extension model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelExtension extends JModelAdmin
{

	protected $_context;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * 
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'NExtension', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// The folder and element vars are passed when saving the form.
		if (!empty($data)) {
			$clientId  = JArrayHelper::getValue($data, 'client_id', '0'); // 0 - means administrator side
			$module    = JArrayHelper::getValue($data, 'module');
			$native    = JArrayHelper::getValue($data, 'native', 0);
			$type      = (JArrayHelper::getValue($data, 'type', 0) == 2)? 'plugin' : 'module';
			$namespace = JArrayHelper::getValue($data, 'namespace', null);
		}

		// These variables are used to add data from the plugin XML files.
		$this->setState('item.client_id', $clientId);
		$this->setState('item.module', $module);
		$this->setState('item.module.native', $native);
		$this->setState('item.module.namespace', $namespace);
		$this->setState('item.nextension.type', $type);

		// Get the form.
		$form = $this->loadForm('com_newsletter.'.$type, $type, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		
		
		$form->setFieldAttribute('layout', 'module', $module, 'params');
		//var_dump($form->getField('layout', 'params')); die;
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.nextension.data', array());
		if (empty($data)) {

			$form = JRequest::getVar('jform');
			if (!empty($form)) {
				$data = $form;
			} else {	
			
				$id     = $this->getState($this->getName().'.id');
				$native = $this->getState('item.module.native');

				$modules = MigurModuleHelper::getSupported(array(
					'extension_id' => $id,
					'native'       => $native
				));

				$data = new JObject($modules[0]);
				if (!empty($data->params)) {
					$data->setProperties($data->params);
				}
				unset($data->params);

			}
		}
		
		return $data;
	}

	/**
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 *
	 * @return	void
	 * @throws	Exception if there is an error loading the form.
	 * @since	1.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = '')
	{
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Initialise variables.
		$module = $this->getState('item.module');

		$clientId = $this->getState('item.client_id', 0);
		$native   = $this->getState('item.module.native', 0);
		$type     = $this->getState('item.nextension.type', 0).'s';
		
		@list($folder) = explode('.', $this->getState('item.module.namespace', null));
		
		$lang		= JFactory::getLanguage();
		$client		= JApplicationHelper::getClientInfo($clientId);

		// Load the core and/or local language file(s).
			$lang->load($module, $client->path, null, false, false)
		||	$lang->load($module, $client->path. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$module, null, false, false)
		||	$lang->load($module, $client->path, $lang->getDefault(), false, false)
		||	$lang->load($module, $client->path. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$module, $lang->getDefault(), false, false);

		$lang->load('com_modules', JPATH_ADMINISTRATOR, $lang->getDefault(), false, true);

		// Create the path to extension's XML manifest
		if ($type == 'modules') {
			$formFile = (!$native)?
				JPath::clean(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'extensions'. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$module. DIRECTORY_SEPARATOR .$module.'.xml') :
				JPath::clean(JPATH_SITE. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$module. DIRECTORY_SEPARATOR .$module.'.xml');
		}

		if ($type == 'plugins') {
			$formFile = (!$native)?
				JPath::clean(JPATH_COMPONENT_ADMINISTRATOR. DIRECTORY_SEPARATOR .'extensions'. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$folder. DIRECTORY_SEPARATOR .$module. DIRECTORY_SEPARATOR .$module.'.xml') :
				JPath::clean(JPATH_SITE. DIRECTORY_SEPARATOR .$type. DIRECTORY_SEPARATOR .$module. DIRECTORY_SEPARATOR .$module.'.xml');
		}
		
		// Get the config part from manifest
		if (file_exists($formFile)) {
			// Get the module form.
			if (!$form->loadFile($formFile, false, '//config')) {
				// TODO deprecated since 12.1 Use PHP Exception
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}

			// Attempt to load the xml file.
			if (!$xml = simplexml_load_file($formFile)) {
				// TODO deprecated since 12.1 Use PHP Exception
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}

			// Get the help data from the XML file if present.
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string       Script files
	 */
	public function getScript()
	{
		return 'administrator/components/com_newsletter/models/forms/extension.js';
	}

}
