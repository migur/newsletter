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
//jimport('joomla.utilities.simplexml');
//jimport('joomla.html.html.sliders');
//JLoader::import('helpers.mail',   JPATH_COMPONENT_ADMINISTRATOR, '');
//JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
//JLoader::import('helpers.plugin', JPATH_COMPONENT_ADMINISTRATOR, '');
//JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');
//JLoader::import('helpers.automailing', JPATH_COMPONENT_ADMINISTRATOR, '');
//JLoader::import('helpers.download', JPATH_COMPONENT_ADMINISTRATOR, '');

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
class NewsletterViewAutomailingItem extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the automailingitem name
	 *
	 * @return void
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		// Set the document
		$this->setDocument();

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		
		// Set main ID first
		$iid = JRequest::getInt('series_id');
		$this->assignRef('seriesId', $iid);

		
		// Get automailing form
		$model = $this->getModel();
		$item = $model->getItem();
		$this->assignRef('item', $item);
		$this->assignRef('form', $this->get('Form', 'automailingitem'));

		
		// Set main ID first
		$aid = !empty($item->automailing_id)? 
			$item->automailing_id : JRequest::getInt('automailing_id');
		$this->assignRef('automailingId', $aid);

		
		// All items of parent (siblings)
		$amItemsModel = JModel::getInstance('AutomailingItems', 'NewsletterModel');
		$amItems = $amItemsModel->getAllItems($aid);
		$this->assignRef('allItems', $amItems);
		
		
		// Automailig entity
		$amItemsModel = JModel::getInstance('Automailing', 'NewsletterModel');
		$am = $amItemsModel->getItem($aid);
		$this->assignRef('automailing', $am);
		
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
		$bar = JToolBar::getInstance('amitem');
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'automailingitem.save', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', 'automailingitem.cancel', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::get('series_id', false) );
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_ADD_AUTOMAILING_ITEM') : JText::_('COM_NEWSLETTER_EDIT_AUTOMAILING_ITEM'));

		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/admin.css');
		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/automailingitem.css');

		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/core.js');
		$document->addScript(JURI::root() . 'administrator/components/com_newsletter/views/automailingitem/automailingitem.js');
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/automailingitem/submitbutton.js");
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/models/forms/automailingitem.js");

		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}
}
