<?php

/**
 * The subscriber view file.
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

/**
 * Class of the subscriber view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewUpdater extends MigurView
{
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
		$this->assign('form', $this->get('Form', 'updater'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 * @since	1.0
	 */
	protected function addToolbar()
	{
//		$bar = MigurToolbar::getInstance();
//		$bar->appendButton('Migurstandard', 'cancel', 'JTOOLBAR_CANCEL', 'subscriber.cancel', false);
//		$bar->appendButton('Migurstandard', 'save', 'JTOOLBAR_SAVE', 'subscriber.save', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();

		$document->setTitle(JText::_('COM_NEWSLETTER_NEW_UPDATER'));

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/updater.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/message.js');

		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}

}
