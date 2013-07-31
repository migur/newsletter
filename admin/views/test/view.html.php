<?php

/**
 * The close view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JHtml::_('behavior.framework', true);

/**
 * Class of the close view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewTest extends JView
{
	/**
	 * Displays the view. Used to close the popup.
	 *
	 * @param  string $tpl the template name
	 * @return void
	 * @since  1.0
	 */
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/test/test.js");		
		$document->addStylesheet(JURI::root() . '/media/com_newsletter/css/admin.css');
		
		parent::display($tpl);
	}
}
