<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidation');
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.helper');
jimport('migur.library.toolbar');
jimport('joomla.html.pagination');
JHtml::_('behavior.framework', true);

// import Joomla view library

/**
 * Newsletter View
 */
class NewsletterViewMailboxprofile extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->ssForm = $this->get('Form', 'mailboxprofile');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();

		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		$bar = JToolBar::getInstance('mailbox-toolbar', 'mailboxprofileForm');
		$bar->appendButton('Standard', 'publish', 'COM_NEWSLETTER_CHECK', 'mailboxprofile.checkconnection', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', '', false);
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'mailboxprofile.save', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::get('mailbox_profile_id', false) );
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_MAILBOX_CREATING') : JText::_('COM_NEWSLETTER_MAILBOX_EDITING'));

		$document->addStylesheet(JURI::root() . '/media/com_newsletter/css/admin.css');
		$document->addStylesheet(JURI::root() . '/media/com_newsletter/css/mailboxprofile.css');

		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/mailboxprofile/submitbutton.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/mailboxprofile/mailboxprofile.js");
		$document->addScript(JURI::root() . '/media/com_newsletter/js/migur/js/core.js');
		
		JText::script('COM_NEWSLETTER_MAILBOX_ERROR_UNACCEPTABLE');
	}

}
