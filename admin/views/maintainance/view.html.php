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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

/**
 * Class of the automailing view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewMaintainance extends MigurView
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
		$this->setDocument();
		
		parent::display($tpl);
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
		
		$document->setTitle(JText::_('COM_NEWSLETTER_MAINTAINANCE'));
		
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/admin.css');
		$document->addstylesheet(JURI::root().'/media/com_newsletter/css/maintainance.css');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/core.js');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/widgets.js');
		$document->addScript(JURI::root().'/media/com_newsletter/js/migur/js/widgets/ajaxchecker.js');
		$document->addScript(JURI::root()."/administrator/components/com_newsletter/views/maintainance/maintainance.js");

		JText::script('COM_NEWSLETTER_AUTOMAILING_ERROR_UNACCEPTABLE');
	}

}
