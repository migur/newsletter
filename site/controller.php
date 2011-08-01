<?php

/**
 * The Common Controller file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('migur.library.mailer');

/**
 * Class of the Common controller.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterController extends JController
{

	/**
	 * The default action in the default controller.
	 *
	 * @param  boolean $cachable
	 * @return object
	 * @since  1.0
	 */
	function display($cachable = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'subscribers'));

		// call parent behavior
		parent::display();

		return $this;
	}
}
