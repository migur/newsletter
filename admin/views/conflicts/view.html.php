<?php

/**
 * The conflicts list view file.
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
 * Class of the conflicts list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewConflicts extends MigurView
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
		JHTML::stylesheet('media/com_newsletter/css/conflicts.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		//JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');
		JHTML::script(JURI::root() . "/administrator/components/com_newsletter/views/conflicts/conflicts.js");

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();

		// Let's work with model 'conflicts' !
		$model = $this->getModel('conflicts');
		$items = $model->getItems();
		$pagination = $model->getPagination();
		$state = $model->getState();
		$listOrder = $model->getState('list.ordering');
		$listDirn = $model->getState('list.direction');
		
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('state', $state);
		$this->assignRef('listOrder', $listOrder);
		$this->assignRef('listDirn', $listDirn);
		$this->assignRef('saveOrder', $saveOrder);
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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_CONFLICT_TITLE'), 'article.png');

		$bar = JToolBar::getInstance();
		// delete all/selected;
		$bar->appendButton('Standard', 'trash', 'COM_NEWSLETTER_DELETE_SUBS', 'conflicts.deletesubs', false);
		// merge selected. preserve J! user's data
		$bar->appendButton('Standard', 'trash', 'COM_NEWSLETTER_MERGE_USERS', 'conflicts.mergeusers', false);
		// merge selected. preserve J! subscriber's data
		$bar->appendButton('Standard', 'trash', 'COM_NEWSLETTER_MERGE_SUBS', 'conflicts.mergesubs', false);

		// TODO merge history (yes/no)		


		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

}
