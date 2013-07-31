<?php

/**
 * The list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import view library
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
jimport('migur.library.toolbar');

/**
 * Class of the list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewInstall extends MigurView
{
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->assign('items', $model->getItems());
		$this->assign('listOrder', $model->getState('list.ordering'));
		$this->assign('listDirn', $model->getState('list.direction'));
		$this->assign('pagination', $model->getPagination());

		$this->addToolbar();

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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_EXTENSION_MANAGER'), 'article.png');

		$bar = JToolBar::getInstance();
		
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'install.remove', false);
		$bar->appendButton('Standard', 'refresh', 'COM_NEWSLETTER_RESTORE', 'install.restore', false);
//		$bar->appendButton('Standard', 'unpublish', 'JTOOLBAR_DISABLE', 'install.unpublish', false);
//		$bar->appendButton('Standard', 'publish', 'JTOOLBAR_ENABLE', 'install.publish', false);
		$bar->appendButton('MigurHelp', 'question-sign', 'COM_NEWSLETTER_HELP', NewsletterHelperSupport::getResourceUrl('extension', 'manager'));

		// Load the submenu.
		NewsletterHelperNewsletter::addSubmenu(JRequest::getVar('view'));
	}
	
	public function setDocument()
	{
		$document = JFactory::getDocument();
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/install/submitbutton.js');

	}
	
}
