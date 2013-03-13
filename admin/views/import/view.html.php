<?php

/**
 * The import view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.import.common', JPATH_COMPONENT_ADMINISTRATOR, '');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework', true);
jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
jimport('migur.library.toolbar');

/**
 * Class of the import view.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewImport extends MigurView
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
		
		JHTML::stylesheet(JURI::root() . 'media/com_newsletter/css/admin.css');
		JHTML::stylesheet(JURI::root() . 'media/com_newsletter/css/import.css');
		JHTML::script(JURI::root() . 'media/com_newsletter/js/migur/js/core.js');
		JHTML::script(JURI::root() . 'media/com_newsletter/js/migur/js/message.js');
		JHTML::script(JURI::root() . 'media/com_newsletter/js/migur/js/iterativeajax.js');
		JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/import/import.js");

		$this->assign('components', NewsletterModelImportCommon::getSupported());

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('sender');
		$bar->appendButton('Link', 'export', 'COM_NEWSLETTER_NEWSLETTER_SEND', '#');
	}

}
