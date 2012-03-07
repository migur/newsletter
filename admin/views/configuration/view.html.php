<?php

/**
 * The configuration view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHTML::_('behavior.modal');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.plugin', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.environment', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of the configuration view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewConfiguration extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the template name
	 * 
	 * @return void
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		JHTML::_('behavior.modal');
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/configuration.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('administrator/components/com_newsletter/views/configuration/configuration.js');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		EnvironmentHelper::showWarnings(array(
			'checkJoomla',
			'checkImap',
			'checkLogs',
			'checkAcl'));
		
		$this->general = JComponentHelper::getParams('com_newsletter');

		//$model = JModel::getInstance('extensions', 'NewsletterModel');
		//$this->modules = $model->getModules();
		//$this->plugins = $model->getPlugins();
		$this->modules = MigurModuleHelper::getSupported();
		$this->plugins = MigurPluginHelper::getSupported();

		$this->form = $this->get('Form');
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_CONFIGURATION_TITLE'), 'article.png');

		$bar = JToolBar::getInstance('toolbar');
		if (AclHelper::canConfigureComponent()) {
			$bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'configuration.apply', false);
		}	

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
