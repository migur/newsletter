<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('migur.library.toolbar');
jimport('joomla.form.helper');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.formvalidation');

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
		$bar = MigurToolbar::getInstance();
		$bar->appendButton('Migurbasic', 'COM_NEWSLETTER_CHECK', array('id' => 'mailbox-toolbar-publish'));
		$bar->appendButton('Migurbasic', 'JTOOLBAR_CANCEL', array('id' => 'mailbox-toolbar-cancel'));
		$bar->appendButton('Migurstandard', 'save', 'JTOOLBAR_SAVE', 'mailboxprofile.save', false);
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

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/mailboxprofile.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/message.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/models/forms/mailboxprofile.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/mailboxprofile/submitbutton.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/mailboxprofile/mailboxprofile.js');

		JText::script('COM_NEWSLETTER_MAILBOX_ERROR_UNACCEPTABLE');
	}

}
