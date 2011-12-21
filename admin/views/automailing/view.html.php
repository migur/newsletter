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
class NewsletterViewAutomailing extends MigurView
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
		// Set the document
		$this->setDocument();

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}
		
		$aid = JRequest::getInt('automailing_id');

		// Get automailing
		$model = $this->getModel();
		$item = $model->getItem();
		$this->assignRef('automailing', $item);
		
		// Get item list (series)
		$itemsModel = JModel::getInstance('AutomailingItems', 'NewsletterModel');
		$amList = (object) array(
				'items' => $itemsModel->getNormalizedItems($aid),
				'state' => $itemsModel->getState(),
				'listOrder' => $itemsModel->getState('list.ordering'),
				'listDirn' => $itemsModel->getState('list.direction')
		);
		$this->assignRef('automailingItems', $amList);

		$pagination = $itemsModel->getPagination();
		$this->assignRef('pagination', $pagination);
		
		// Get target list
		$targetsModel = JModel::getInstance('AutomailingTargets', 'NewsletterModel');
		$targetsModel->automailingId = $aid;
		$this->assignRef('automailingTargets', $targetsModel->getNames($aid));
		
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
		$isNew = (!JRequest::getInt('newsletter_id', false) );
		JToolBarHelper::title($isNew? 
			JText::_('COM_NEWSLETTER_NEWSLETTERS_ADD_TITLE') : 
			JText::_('COM_NEWSLETTER_NEWSLETTERS_EDIT_TITLE'), 
		'article.png');

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'autosaver', '', '#', false);
		$bar->appendButton('Separator', null, '50');
		$bar->appendButton('Link', 'apply', 'JTOOLBAR_APPLY', '#', false);
		$bar->appendButton('Standard', 'save',  'JTOOLBAR_SAVE', 'newsletter.save', false);
		$bar->appendButton('Standard', 'default', 'COM_NEWSLETTER_TUTORIAL', '', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', 'newsletter.cancel', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::getInt('automailing_id', false) );
		JavascriptHelper::addStringVar('isNew', (int)$isNew);
		$document = JFactory::getDocument();
		
		$document->setTitle($isNew? JText::_('COM_NEWSLETTER_AUTOMAILING_CREATING') : JText::_('COM_NEWSLETTER_AUTOMAILING_EDITING'));
		
		$document->addScript(JURI::root()."/administrator/components/com_newsletter/views/automailing/automailing.js");
		$document->addScript(JURI::root()."/administrator/components/com_newsletter/views/automailing/submitbutton.js");
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/admin.css');
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/automailing.css');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/core.js');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/ajax.js');

		JText::script('COM_NEWSLETTER_AUTOMAILING_ERROR_UNACCEPTABLE');
	}

}
