<?php

/**
 * The controller for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

JLoader::import('tables.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerBounceds extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
	}

	
	public function process() {
		
		$cids = JRequest::getVar('cid', array());
		
		if (empty($cids)) {
			return false;
		}
		
		$queues = JModel::getInstance('Queues', 'NewsletterModel');
		
		foreach($cids as $cid) {
			
			
			
		}
		
	}
	
}
