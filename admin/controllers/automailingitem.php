<?php

/**
 * The controller for automailing view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerAutomailingItem extends JControllerForm
{
	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function save()
	{
		$jform = JRequest::getVar('jform', array(), 'post', 'array');
		$iid = $jform['series_id'];
		$aid = $jform['automailing_id'];
		
		if (parent::save()) {
			
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=close&tmpl=component', false));
					break;
			}

			return true;
			
		} else {
			
			$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=' . $this->view_item . $this->getRedirectToItemAppend($iid, 'series_id') . '$automailing_id='.$aid, false));
			
		}

		return false;
	}
	
}

