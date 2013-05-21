<?php

/**
 * The logs list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHTML::_('behavior.modal');

/**
 * Class of the logs list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewLogs extends MigurView
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
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Let's work with model 'logs' !
		$model = $this->getModel('logs');
		$items = $model->getItems();
		$categories = $model->getCategories();
		$pagination = $model->getPagination();
		$state = $model->getState();
		$listOrder = $model->getState('list.ordering');
		$listDirn = $model->getState('list.direction');
		$priorities = $model->getPriorities();
		
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('state', $state);
		$this->assignRef('listOrder', $listOrder);
		$this->assignRef('listDirn', $listDirn);
		$this->assignRef('saveOrder', $saveOrder);
		$this->assignRef('categories', $categories);
		$this->assignRef('priorities', $priorities);

		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
			$this->addSidebar();
			$this->sidebar = JHtmlSidebar::render();
		}

		$this->setDocument();
		
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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_LOG_TITLE'), 'article.png');

		$bar = JToolBar::getInstance();
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'logs.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}
	
	protected function addSidebar() 
	{
		JHtmlSidebar::setAction('index.php?option=com_newsletter&view=logs');

		JHtmlSidebar::addFilter(
			JText::_('COM_NEWSLETTER_FILTER_ON_CATEGORY'),
			'filter_category',
			JHtml::_('select.options', JHtml::_('multigrid.generalOptions', $this->categories, null), 'value', 'text', $this->state->get('filter.category'), true)
		);

		JHtmlSidebar::addFilter(
			JText::_('COM_NEWSLETTER_FILTER_ON_TYPES'),
			'filter_priority',
			JHtml::_('select.options', JHtml::_('multigrid.generalOptions', $this->priorities), 'value', 'text', $this->state->get('filter.priority'), true)
		);
	}

	protected function setDocument() 
	{
		$doc = JFactory::getDocument();
		
		$doc->addStyleSheet(JUri::root() . 'media/com_newsletter/css/admin.css');
		$doc->addStyleSheet(JUri::root() . 'media/com_newsletter/css/logs.css');
		$doc->addScript(JUri::root() . 'media/com_newsletter/js/migur/js/core.js');
		$doc->addScript(JUri::root() . 'administrator/components/com_newsletter/views/logs/logs.js');
	}
	
}
