<?php

/**
 * The subscribers list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');

JLoader::import('helpers.environment', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of the subscribers list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewSubscribers extends MigurView
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
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/subscribers.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');		
		JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/subscribers/subscribers.js");


		$this->setModel(
			JModel::getInstance('lists', 'NewsletterModel')
		);

		EnvironmentHelper::showWarnings(array(
			'checkUserConflicts'));
		
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		$modelSubs = $this->getModel('subscribers');
		$modelLists = $this->getModel('lists');

		$ss = (object) array(
				'items' => $modelSubs->getItems(),
				'pagination' => $modelSubs->getPagination(),
				'state' => $modelSubs->getState(),
				'listOrder' => $modelSubs->getState('list.ordering'),
				'listDirn' => $modelSubs->getState('list.direction')
		);
		$this->assignRef('subscribers', $ss);

		$lists = (object) array(
				'items' => $modelLists->getItems(),
				'pagination' => $modelLists->getPagination(),
				'state' => $modelLists->getState(),
				'listOrder' => $modelLists->getState('list.ordering'),
				'listDirn' => $modelLists->getState('list.direction')
		);
		$this->assignRef('lists', $lists);

		$this->assignRef('subscriberModel', JModel::getInstance('Subscriber', 'NewsletterModelEntity'));
		
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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_SUBSCRIBERS_TITLE'), 'article.png');

		$bar = MigurToolBar::getInstance('subscribers', null, '');
		
		if (AclHelper::actionIsAllowed('list.edit')) {
			$bar->appendButton('Link', 'cancel', 'COM_NEWSLETTER_REMOVE_FROM_LIST', 'list.unbindgroup', false);
			$bar->appendButton('Link', 'copy', 'COM_NEWSLETTER_ASSIGN_TO_LIST', 'list.assigngroup', false);
		}
		
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&amp;task=subscriber.add&amp;tmpl=component', 400, 220, 0, 0);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'subscribers.delete', false);
		$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'subscribers.unpublish', false);
		$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'subscribers.publish', false);
		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', 'http://migur.com/support/documentation/newsletter');

		$bar = MigurToolBar::getInstance('lists');
		
		if (AclHelper::actionIsAllowed('list.add')) {
			$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&amp;view=list&amp;tmpl=component', 1000, 600, 0, 0);
		}
		
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'lists.delete', false);
		
		if (AclHelper::actionIsAllowed('list.edit')) {
			$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'lists.unpublish', false);
			$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'lists.publish', false);
		}	

		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', 'http://migur.com/support/documentation/newsletter');

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
