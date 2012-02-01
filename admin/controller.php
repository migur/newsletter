<?php

/**
 * @version		$Id: controller.php 74 2010-12-01 22:04:52Z chdemko $
 * @package		Joomla16.Tutorials
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

class NewsletterController extends JController
{

	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'dashboard'));

		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		$skipCheck = false;
		if ($layout == 'edit') {
			switch ($view) {

				case 'extension':
					/* We cant check out the extension because it may be used in JS rendering
					  $idName = 'extension_id';
					  $viewRedir = 'extensions';
					 */
					$skipCheck = true;
					break;

				case 'list':
					$idName = 'list_id';
					$viewRedir = 'lists';
					break;

				case 'newsletter':
					$idName = 'newsletter_id';
					$viewRedir = 'newsletters';
					break;

				case 'subscriber':
					$idName = 'subscriber_id';
					$viewRedir = 'subscribers';
					break;

				case 'template':
					$idName = 't_style_id';
					$viewRedir = 'templates';
					break;
				
				case 'automailing':
					$idName = 'automailing_id';
					$viewRedir = 'automailings';
					break;
			}

			if (empty($skipCheck) && !empty($idName)) {
					$id = JRequest::getCmd($idName);
				if (!$this->checkEditId('com_newsletter.edit.' . $view, $id)) {
					// Somehow the person just went to the form - we don't allow that.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
					$this->setMessage($this->getError(), 'error');
					$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=' . $viewRedir, false));
					return false;
				}
			}
		}

		// call parent behavior
		parent::display();

		return $this;
	}
}
