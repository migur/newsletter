<?php
/**
 * @package    Joomla.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Set the platform root path as a constant if necessary.
if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', dirname(__FILE__));
}

// Detect the native operating system type.
$os = strtoupper(substr(PHP_OS, 0, 3));
if (!defined('IS_WIN'))
{
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_MAC'))
{
	define('IS_MAC', ($os === 'MAC') ? true : false);
}
if (!defined('IS_UNIX'))
{
	define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

// Import the platform version library if necessary.
if (!class_exists('JPlatform'))
{
	require_once JPATH_PLATFORM . '/platform.php';
}

// Import the library loader if necessary.
if (!class_exists('JLoader'))
{
	require_once 'loader.php';
}

class_exists('JLoader') or die;

// Setup the autoloaders.
JLoader::setup();

// Import the base Joomla Platform libraries.
JLoader::import('joomla.factory');
JLoader::import('joomla.application.component.helper');
JLoader::import('joomla.application.component.modellist');
JLoader::import('joomla.application.component.controller');
JLoader::import('joomla.application.component.view');
JLoader::import('joomla.form.helper');

// Register classes that don't follow one file per class naming conventions.
JLoader::register('JObject', JPATH_LIBRARIES . '/joomla/base/object.php');
JLoader::register('JRoute', JPATH_LIBRARIES . '/joomla/methods.php');
JLoader::register('JText', JPATH_LIBRARIES . '/joomla/methods.php');
JLoader::register('JRoute', JPATH_LIBRARIES . '/joomla/application/route.php');
JLoader::register('JError', JPATH_LIBRARIES . '/legacy/error/error.php');
JLoader::register('JException', JPATH_LIBRARIES . '/legacy/exception/exception.php');
JLoader::register('JRequest', JPATH_LIBRARIES . '/joomla/environment/request.php');

JLoader::import('migur.migur');
