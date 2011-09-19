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
JHtml::_('behavior.framework');
JHtml::_('behavior.tooltip');
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');

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
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');		
		JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/subscribers/subscribers.js");


		$this->setModel(
			JModel::getInstance('lists', 'NewsletterModel')
		);

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

		$bar = MigurToolBar::getInstance('subscribers');
		$bar->appendButton('Link', 'cancel', 'COM_NEWSLETTER_REMOVE_FROM_LIST', 'list.unbindgroup', false);
		$bar->appendButton('Link', 'copy', 'COM_NEWSLETTER_ASSIGN_TO_LIST', 'list.assigngroup', false);
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&amp;view=subscriber&amp;tmpl=component', 350, 150, 0, 0);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'subscribers.delete', false);
		$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'subscribers.unpublish', false);
		$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'subscribers.publish', false);
                
                $helpLink = 'http://migur.com/support/documentation/newsletter/' . NewsletterHelper::getManifest()->version . '/subscribers';
		$bar->appendButton('Popup', 'help', 'COM_NEWSLETTER_HELP', $helpLink, 1000, 600, 0, 0);

		$bar = MigurToolBar::getInstance('lists');
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&amp;view=list&amp;tmpl=component', 1000, 600, 0, 0);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'lists.delete', false);
		$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'lists.unpublish', false);
		$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'lists.publish', false);
                
                $helpLink = 'http://migur.com/support/documentation/newsletter/' . NewsletterHelper::getManifest()->version . '/lists-user';
		$bar->appendButton('Popup', 'help', 'COM_NEWSLETTER_HELP', $helpLink, 1000, 600, 0, 0);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
