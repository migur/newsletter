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
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/configuration.css');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/modal.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/configuration/configuration.js');

		// Add JS and create namespace for data
		//$document = JFactory::getDocument();
		//NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/support.js');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		NewsletterHelperEnvironment::showWarnings(array(
			'checkJoomla',
			'checkImap',
			'checkLogs',
			'checkAcl'));

		$this->assign('general', JComponentHelper::getParams('com_newsletter'));

		//$model = MigurModel::getInstance('extensions', 'NewsletterModel');
		//$this->modules = $model->getModules();
		//$this->plugins = $model->getPlugins();
		$this->assign('modules', NewsletterHelperModule::getSupported());
		$this->assign('plugins', MigurPluginHelper::getSupported());

		$this->assign('templates', MigurModel::getInstance('Templates', 'NewsletterModel')->getAllInstalledItems());

		$this->assign('form', $this->get('Form'));
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

		$bar = MigurToolbar::getInstance('toolbar');
		if (NewsletterHelperAcl::canConfigureComponent()) {
			$bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'configuration.apply', false);
		}

		// Load the submenu.
		NewsletterHelperNewsletter::addSubmenu(JRequest::getVar('view'));
	}

}
