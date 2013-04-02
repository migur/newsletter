<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.helper');
jimport('migur.library.toolbar');
jimport('joomla.html.pagination');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.formvalidation');

// import Joomla view library

/**
 * Newsletter View
 */
class NewsletterViewSmtpprofile extends MigurView
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
		$this->ssForm = $this->get('Form', 'smtpprofile');
		
		$model = MigurModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
		$smtpid = JRequest::getInt('smtp_profile_id', null);
		
		if ($smtpid !== null) {
			$model->load($smtpid);
		}	
		
		JavascriptHelper::addStringVar('migurIsJoomlaProfile', $model->isJoomlaProfile());
		
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
		$bar = JToolBar::getInstance();
		$bar->addButtonPath(COM_NEWSLETTER_PATH_ADMIN . '/helpers/toolbar/button');
		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', SupportHelper::getResourceUrl('smtpp', 'general'));
		$bar->appendButton('Basic', 'COM_NEWSLETTER_CHECK', array('id' => 'smtp-toolbar-publish'));
		$bar->appendButton('Basic', 'JTOOLBAR_CANCEL', array('id' => 'smtp-toolbar-cancel'));
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'smtpprofile.save', false);
	}

	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::get('smtp_profile_id', false) );
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_SMTP_CREATING') : JText::_('COM_NEWSLETTER_SMTP_EDITING'));
		$document->addStyleSheet(JURI::root() . "media/com_newsletter/css/admin.css");
		$document->addStyleSheet(JURI::root() . "media/com_newsletter/css/smtpprofile.css");
		$document->addScript(JURI::root() . "media/com_newsletter/js/migur/js/core.js");
		$document->addScript(JURI::root() . "media/com_newsletter/js/migur/js/message.js");
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/smtpprofile/submitbutton.js");
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/smtpprofile/smtpprofile.js");
		JText::script('COM_NEWSLETTER_MAILBOX_ERROR_UNACCEPTABLE');
	}

}
