<?php

/**
 * The sender view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('models.fields.newsletters', JPATH_COMPONENT_ADMINISTRATOR, '');

// import Joomla view library
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
jimport('joomla.application.component.view');
jimport('joomla.html.pagination');
jimport('migur.library.toolbar');
JHtml::_('behavior.framework', true);

/**
 * Class of the  view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewSender extends MigurView
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
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/sender.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');		
		JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/sender/sender.js");


		$this->setModel(
			JModel::getInstance('lists', 'NewsletterModel')
		);

		$this->setModel(
			JModel::getInstance('newsletters', 'NewsletterModel')
		);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$modelLists = $this->getModel('lists');

		JRequest::setVar('limit', 1);
		$limit = $modelLists->setState('limit', 1);
		$modelLists->filtering = array('state' => '1');

		$lists = (object) array(
				'items' => $modelLists->getItems(),
				'pagination' => new JPagination(10, 0, 5), // used to get the pagination layout for JS pagination
				'state' => $modelLists->getState(),
				'listOrder' => $modelLists->getState('list.ordering'),
				'listDirn' => $modelLists->getState('list.direction')
		);
		
		JavascriptHelper::addStringVar('defaultMailbox', MailHelper::getDefaultMailbox('idOnly'));

		$modelLists->setState('limit', $limit);

		$this->assignRef('lists', $lists);

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

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}
}
