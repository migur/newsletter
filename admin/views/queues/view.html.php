<?php

/**
 * The queues list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
JLoader::import('helpers.statistics', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.environment', JPATH_COMPONENT_ADMINISTRATOR, '');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

/**
 * Class of the queues list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewQueues extends MigurView
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
		JHTML::stylesheet('media/com_newsletter/css/queues.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');
		JHTML::script(JURI::root() . "/administrator/components/com_newsletter/views/queues/queues.js");

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		EnvironmentHelper::showWarnings(array(
			'checkJoomla',
			'checkImap',
			'checkLogs'));
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

//		JHTML::_('behavior.modal');

		// Let's work with model 'queues' !
		$model = $this->getModel('queues');
		$items = $model->getItems();
		$pagination = $model->getPagination();
		$state = $model->getState();
		$listOrder = $model->getState('list.ordering');
		$listDirn = $model->getState('list.direction');
                
		$saveOrder = $listOrder == 'a.ordering';

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('state', $state);
		$this->assignRef('listOrder', $listOrder);
		$this->assignRef('listDirn', $listDirn);
		$this->assignRef('saveOrder', $saveOrder);

		$sess = JFactory::getSession();
		JavascriptHelper::addStringVar('sessname', $sess->getName());

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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_QUEUE_TITLE'), 'article.png');

		$bar = JToolBar::getInstance();
		$bar->appendButton('Custom', '', 'preloader', '');
		$bar->appendButton('Link', 'messaging', 'COM_NEWSLETTER_PROCESS_QUEUE', '#');
		$bar->appendButton('Link', 'alert', 'COM_NEWSLETTER_PROCESS_BOUNCES', '#');
		$bar->appendButton('Separator', null, '30');
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'queues.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
