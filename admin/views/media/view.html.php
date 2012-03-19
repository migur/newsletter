<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JHtml::_('behavior.framework', true);

/**
 * HTML View class for the Media component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class NewsletterViewMedia extends JView
{
	function display($tpl = null)
	{
		JavascriptHelper::addStringVar('migurFieldId', JRequest::getString('fieldId', 'insertField'));
		
		JFactory::getDocument()->addScript(JUri::root()."/administrator/components/com_newsletter/views/media/media.js");
		
		parent::display($tpl);
	}
}
