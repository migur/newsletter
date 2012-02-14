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

		$this->addToolbar();
		
		// Set main ID first
		$aid = JRequest::getInt('automailing_id');
		$this->assignRef('automailingId', $aid);
		
		
		// Get automailing form
		$model = $this->getModel();
		$automailing = $model->getItem();
		$this->assignRef('automailing', $automailing);
		JavascriptHelper::addObject('automailing', $automailing);
		$this->assignRef('form', $this->get('form', 'automailing'));
		
		
		// Get item list (series)
		$itemsModel = JModel::getInstance('AutomailingItems', 'NewsletterModel');
		$itemsModel->automailingId = $aid;
		$amList = (object) array(
				'items' => $itemsModel->getNormalizedItems($aid),
				'state' => $itemsModel->getState(),
				'listOrder' => $itemsModel->getState('list.ordering'),
				'listDirn' => $itemsModel->getState('list.direction'),
				'pagination' => $itemsModel->getPagination()
		);
		$this->assignRef('automailingItems', $amList);
		

		// Get targets list
		$targetsModel = JModel::getInstance('AutomailingTargets', 'NewsletterModel');
		$targetsModel->automailingId = $aid;
		
		if ($tpl != 'details') {	
			
			// Get ids for all available lists
			$listsModel = JModel::getInstance('Lists', 'NewsletterModel');
			$allLists = $listsModel->getAllActive();
			
			// Find all used lists
			$usedLists = $targetsModel->getRelatedLists($aid);
			
			// Diff the records
			$usedListIds = DataHelper::getColumnData($usedLists, 'list_id');
			
			foreach($allLists as $idx => $item) {
				if (in_array($item->list_id, $usedListIds)) {
					unset($allLists[$idx]);
				}
			}
			
			$amTargets = (object) array(
					'items' => $targetsModel->getRelatedLists($aid, 'usePagination'),
					'state' => $targetsModel->getState(),
					'listOrder' => $targetsModel->getState('list.ordering'),
					'listDirn' => $targetsModel->getState('list.direction'),
					'pagination' => $targetsModel->getPagination()
			);
			
			$this->assignRef('automailingTargets', $amTargets);
			$this->assignRef('unusedLists', $allLists);
			
		}	

		
		if ($tpl == 'details') {	
			$this->assignRef('automailingTargets', $targetsModel->getNames($aid));
		}	
		
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
		$aid = JRequest::getInt('automailing_id');

		$bar = JToolBar::getInstance('automailing');
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'automailing.save', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', 'automailing.cancel', false);
		
		$bar = JToolBar::getInstance('series');
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&view=automailingitem&layout=edit&tmpl=component&automailing_id='.$aid, 400, 200, 0, 0);
		
		$bar = JToolBar::getInstance('lists');
		$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_newsletter&view=automailing&tmpl=component', 880, 680, 0, 0);
		$bar->appendButton('Link', 'edit', 'JTOOLBAR_EDIT', 'template.edit', false);
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'templates.delete', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::getInt('automailing_id', false));
		JToolBarHelper::title($isNew? 
			JText::_('COM_NEWSLETTER_AUTOMAILING_ADD_TITLE') : 
			JText::_('COM_NEWSLETTER_AUTOMAILING_EDIT_TITLE'), 
		'article.png');
		
		JavascriptHelper::addStringVar('isNew', (int)$isNew);
		
		$document = JFactory::getDocument();
		
		$document->setTitle($isNew? JText::_('COM_NEWSLETTER_AUTOMAILING_CREATING') : JText::_('COM_NEWSLETTER_AUTOMAILING_EDITING'));
		
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/admin.css');
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/automailing.css');
		$document->addScript(JURI::root()."/administrator/components/com_newsletter/views/automailing/automailing.js");
		$document->addScript(JURI::root()."/administrator/components/com_newsletter/views/automailing/submitbutton.js");
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/core.js');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/ajax.js');

		JText::script('COM_NEWSLETTER_AUTOMAILING_ERROR_UNACCEPTABLE');
	}

}
