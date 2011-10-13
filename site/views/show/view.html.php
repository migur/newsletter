<?php
/**
 * @version		$Id:  $
 * @package		Components
 * @subpackage	com_newsletter
 * @copyright	Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * HTML Subscribe View class for the Newsletter component
 *
 * @package		Newsletter.Site
 * @subpackage	com_newsletter
 * @since 		1.0
 */
class NewsletterViewShow extends JView
{
	function display($tpl = null)
	{
		/*
		 * Get the info about current user.
		 * Check if the user is admin.
		 */

		$alias = JRequest::getString('alias', null);

		if (!empty($alias)) {

			$newslettter = NewsletterHelper::getByAlias($alias);
			
			$this->assignRef('newsletter', $newslettter);
			
			parent::display();
		}	
	}
}
