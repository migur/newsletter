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
		$listModel = MigurModel::getInstance('lists', 'NewsletterModel');
		$this->setModel($listModel);

		NewsletterHelperEnvironment::showWarnings(array(
			'checkUserConflicts'));
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
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

		$this->assign('activationIsAllowed', $modelSubs->getState()->get('filter.list') > 0);		
		
		$this->assign('subscriberModel', MigurModel::getInstance('Subscriber', 'NewsletterModelEntity'));
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_SUBSCRIBERS_TITLE'), 'article.png');

		$bar = MigurToolBar::getInstance('subscribers', null, '');

		$bar->appendButton('MigurModal', 'COM_NEWSLETTER_NEW_SUBSCRIBER', array(
			'url' => 'index.php?option=com_newsletter&task=subscriber.add&tmpl=component',
			'modal' => '#modal-subscriber',
			'class' => 'btn btn-small btn-success',
			'icon-class' => 'icon-new'
		));
		
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'subscribers.delete', false);
		$bar->appendButton('Standard', 'unblock', 'JTOOLBAR_ENABLE', 'subscribers.publish', false);
		$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'subscribers.unpublish', false);

		if ($this->activationIsAllowed) {
			$bar->appendButton('Standard', 'publish', 'COM_NEWSLETTER_ACTIVATE', 'lists.activate', false);
		}

        if (NewsletterHelperAcl::actionIsAllowed('list.edit')) {
			$bar->appendButton('MigurBasic', 'COM_NEWSLETTER_REMOVE_FROM_LIST', array(
				'id' => 'subscribers-unbind', 
				'data-task' => 'list.unbindgroup',
				'icon-class' => 'icon-cancel'
			));
			
			$bar->appendButton('MigurBasic', 'COM_NEWSLETTER_ASSIGN_TO_LIST', array(
				'id' => 'subscribers-assign', 
				'data-task' => 'list.assigngroup',
				'icon-class' => 'icon-copy'
			));
		}
		
		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', NewsletterHelperSupport::getResourceUrl('subscribers'));

		$bar = MigurToolBar::getInstance('lists');
		
		if (NewsletterHelperAcl::actionIsAllowed('list.add')) {
			$bar->appendButton('Standard', 'new', 'COM_NEWSLETTER_NEW_LIST_CREATE', 'list.add', false);
		}
		
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'lists.delete', false);
		
		if (NewsletterHelperAcl::actionIsAllowed('list.edit')) {
			$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'lists.unpublish', false);
			$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'lists.publish', false);
		}	

		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', NewsletterHelperSupport::getResourceUrl('lists'));

		// Load the submenu.
		NewsletterHelperNewsletter::addSubmenu(JRequest::getVar('view'));
	}
	
	public function setDocument() 
	{
		$doc = JFactory::getDocument();
		
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/subscribers.css');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/modal.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/filterpanel.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/subscribers/subscribers.js');
	}

}
