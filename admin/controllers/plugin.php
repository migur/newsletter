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

            $manager = NewsletterDispatcher::factory($pGroup);

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
	
    public function triggerListimport()
    {
        $pGroup = 'list';
        $pName = JRequest::getString('pluginname', null);
		$pEvent = JRequest::getString('pluginevent', null);

        JLoader::import('plugins.manager', JPATH_COMPONENT_ADMINISTRATOR, '');

        $manager = NewsletterPluginManager::factory('import');

        // Trigger event for plugin
        $context = $this->option.'.edit.'.$this->context;
        $listId = JRequest::getInt('list_id'); 
        $res = $manager->trigger(
            array(
                'name'  => $pName,
                'group' => $pGroup,
                'event' => $pEvent),
            array(
                $listId,
                (array) JRequest::getVar('jform')
        ));

        // In this case we trigger only one plugin then his data is in first element
        $res = $res[0];
        
        
        // Get VIEW.....
        // Set layout for event
        $pEvent = strtolower(str_replace('onMigurImport', '', $manager->pluginEvent));
        
		JRequest::setVar('view', 'plugin');
		JRequest::setVar('layout', $pEvent);
		
		$view = $this->getView(
            'plugin', 'html', '', 
            array(
                'base_path' => $this->basePath, 
                'layout' => 'listimport-'.strtolower($pEvent)
        ));

        // Get all view need...
        $plg = $manager->getPlugin($pName, $pGroup);
        $plugin = new stdClass();
        $plugin->data = (array) $res;
        $plugin->name = (string) $pName;
        $plugin->group = (string) $pGroup;
        $plugin->title = $plg->getTitle();
        
        // Complement data
        $plugin->description = empty($res->description)? $plg->getDescription() : $res['description'];
        
        // Set all view need...
        $view->assignRef('plugin', $plugin);
        $view->assign('listId', $listId);
        
        return $this->display();
    }
}
