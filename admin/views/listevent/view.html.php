<?php

/**
 * The automailing view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('migur.library.toolbar');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');

/**
 * Class of the automailing view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewListevent extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the listevent name
	 *
	 * @return void
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		// Set the document
		$this->setDocument();

		// Get automailing form
		$model = $this->getModel();
		$item = $model->getItem();
		$this->assignRef('item', $item);
		$this->assign('form', $this->get('Form', 'listevent'));

		
		// Set main ID first
		$lid = !empty($item->list_id)? $item->list_id : JRequest::getInt('list_id');
		$leid = !empty($item->le_id)? $item->le_id : JRequest::getInt('le_id');
		$this->assignRef('listId', $lid);
		$this->assignRef('listeventId', $leid);

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
		$bar = JToolBar::getInstance('listevent');
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'listevent.save', false);
		$bar->appendButton('Standard', 'save-new', 'JTOOLBAR_SAVE_AND_NEW', 'listevent.save2new', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', 'listevent.cancel', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::get('listevent_id', false) );
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_ADD_LISTEVENT') : JText::_('COM_NEWSLETTER_EDIT_LISTEVENT'));

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/listevent.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/listevent/listevent.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/listevent/submitbutton.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/models/forms/listevent.js');

		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}
}
