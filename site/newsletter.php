<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//  Uncoment this rows for debug
//  ini_set('error_reporting', E_ALL | E_STRICT | E_NOTICE | E_DEPRECATED);
//  ini_set('display_errors', '1');
//  ini_set("log_errors" , "0");
//  ini_set("error_log" , "/var/log/php-error.log");

// import joomla controller library
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
jimport('joomla.form.helper');
jimport('migur.migur');
jimport('joomla.error.log');

// Add the helper
JLoader::import('helpers.javascript', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.rssfeed', JPATH_COMPONENT_ADMINISTRATOR, '');

JFormHelper::addRulePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'rules');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');
JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models');

// Get an instance of the controller prefixed by Newsletter
$controller = JController::getInstance('Newsletter');

// Add translations used in JavaScript
JavascriptHelper::requireTranslations();

// Load 'Migur' group of plugins
JPluginHelper::importPlugin('migur');
$app = JFactory::getApplication();
$app->triggerEvent('onMigurNewsletterStart');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

$app->triggerEvent('onMigurNewsletterEnd');

// Redirect if set by the controller
$controller->redirect();