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


class NewsletterControllerPlugin extends JControllerForm
{
	public function trigger()
	{
		$pName = JRequest::getString('triggername', null);
		$pGroup = JRequest::getString('triggergroup', null);
		$pEvent = JRequest::getString('triggerevent', null);

        if (!empty($pGroup) && !empty($pEvent)) {       
        
            JLoader::import('plugins.manager', JPATH_COMPONENT_ADMINISTRATOR, '');

            $manager = NewsletterPluginManager::factory($pGroup);

            $manager->trigger(array(
                'name'  => $pName,
                'group' => $pGroup,
                'event' => $pEvent),
                JRequest::get()
            );

		} else {
			header ("HTTP/1.0 505 Internal server error");
        }

        jexit();
	}
}
