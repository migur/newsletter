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

/**
 * HTML Subscribe View class for the Newsletter component
 *
 * @package		Newsletter.Site
 * @subpackage	com_newsletter
 * @since 		1.0
 */
class NewsletterViewProfile extends JView
{
	function display($tpl = null)
	{
		// Get the view data.
		
		$user = JFactory::getUser();
		var_dump($user);
		$this->data		= $this->get('Data');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Check if a user was found.
		if (!$this->data->id) {
			JError::raiseError(404, JText::_('JERROR_USERS_PROFILE_NOT_FOUND'));
			return false;
		}
	}
}
