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

		//TODO: Need to move css/js to SetDocument

		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/logs.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		//JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');
		JHTML::script(JURI::root() . "/administrator/components/com_newsletter/views/logs/logs.js");

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
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

		$bar = JToolBar::getInstance('logs');
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'logs.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
