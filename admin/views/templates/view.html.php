<?php

/**
 * The templates list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');

/**
 * Class of the templates list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewTemplates extends MigurView
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

		//TODO: Need to move css/js to SetDocument

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/templates.css');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/templates/templates.js');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		$modelTemps = $this->getModel('templates');

		$pagination = $modelTemps->getPagination();

		
		$temps = (object) array(
				'items' => $modelTemps->getItems(),
				'state' => $modelTemps->getState(),
				'listOrder' => $modelTemps->getState('list.ordering'),
				'listDirn' => $modelTemps->getState('list.direction')
		);
		$this->assignRef('templates', $temps);
		$this->assignRef('pagination', $pagination);

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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_TEMPLATES_TITLE'), 'article.png');

		$bar = JToolBar::getInstance();
		$bar->appendButton('Standard', 'new', 'JTOOLBAR_NEW', 'template.add', false);
		//$bar->appendButton('Standard', 'edit', 'JTOOLBAR_EDIT', 'template.edit', false);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'templates.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
