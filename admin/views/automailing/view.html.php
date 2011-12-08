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
		//TODO: Need to move css/js to SetDocument

		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/automailing.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/ajax.js');

		// Set the document
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
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/automailing/automailing.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/automailing/submitbutton.js");
		JText::script('COM_NEWSLETTER_AUTOMAILING_ERROR_UNACCEPTABLE');
	}

}
