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

class NewsletterControllerUpdater extends JControllerForm
{
	/**
	 * Returns info about updates
	 */
	public function getUpdates()
	{
		NewsletterHelper::jsonPrepare();
		
		$scope = (array) JRequest::getVar('scope', array('com_newsletter'));
		
		$cache = JFactory::getCache('com_newsletter');

		$ext = JTable::getInstance('Extension', 'JTable');

		$data = array();
		
		foreach($scope as $element) {
		
			if ($ext->load(array('element' => $element))) {
				
				$data[$element] = $cache->call(
					array('NewsletterHelper', 'findUpdate'),
					$ext->extension_id
				);
			}	
		}
		
		NewsletterHelper::jsonMessage('', $data);	
	}
}

