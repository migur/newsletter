<?php

/**
 * The automailings list view file.
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
JHTML::_('behavior.modal');
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');

/**
 * Class of the automailings list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewAutomailings extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the automailing name
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

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		$model = $this->getModel('automailings');
		
		$amList = (object) array(
				'items' => $model->getItems(),
				'state' => $model->getState(),
				'listOrder' => $model->getState('list.ordering'),
				'listDirn' => $model->getState('list.direction')
		);
		$this->assignRef('automailings', $amList);
		
		$pagination = $model->getPagination();
		$this->assignRef('pagination', $pagination);
		
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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_AUTOMAILINGS_TITLE'), 'article.png');

		$bar = JToolBar::getInstance('automailings');
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&view=automailing&tmpl=component', 350, 175, 0, 0);
		$bar->appendButton('Link', 'edit', 'JTOOLBAR_EDIT', 'automailing.edit', false);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'automailings.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}
	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/automailings.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/automailings/automailings.js");
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');		
	}
}
