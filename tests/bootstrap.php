<?php
/**
 * Prepares a minimalist framework for unit testing.
 *
 * Joomla is assumed to include the /unittest/ directory.
 * eg, /path/to/joomla/unittest/
 *
 * @package     Joomla.UnitTest
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        http://www.phpunit.de/manual/current/en/installation.html
 */

define('_JEXEC', 1);

// Fix magic quotes.
@ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

/*
 * Ensure that required path constants are defined.  These can be overridden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
define('JOOMLA_PATH', dirname(dirname(dirname(__FILE__))) . '/joomla');
define('JPATH_ADMINISTRATOR', JOOMLA_PATH . '/administrator');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_newsletter');

define('NEWSLETTER_SRC', dirname(dirname(__FILE__)));
define('NEWSLETTER_TESTS', dirname(__FILE__));
define('JPATH_NEWSLETTER_MOCKS', NEWSLETTER_TESTS . '/core/mock/com_newsletter');

if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', JOOMLA_PATH . '/libraries');
}
if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', NEWSLETTER_TESTS . '/tmp');
}
if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', /*JPATH_BASE*/ JOOMLA_PATH);
}
if (!defined('JPATH_CACHE'))
{
	define('JPATH_CACHE', JPATH_BASE . '/cache');
}
if (!defined('JPATH_CONFIGURATION'))
{
	define('JPATH_CONFIGURATION', JPATH_BASE);
}
if (!defined('JPATH_MANIFESTS'))
{
	define('JPATH_MANIFESTS', JPATH_BASE . '/manifests');
}
if (!defined('JPATH_PLUGINS'))
{
	define('JPATH_PLUGINS', JPATH_BASE . '/plugins');
}
if (!defined('JPATH_THEMES'))
{
	define('JPATH_THEMES', JPATH_BASE . '/themes');
}

if (!defined('JPATH_MOCKS'))
{
	define('JPATH_MOCKS', NEWSLETTER_TESTS . '/core/mock');
}

if (!defined('JPATH_COMPONENT_MOCKS'))
{
	define('JPATH_COMPONENT_MOCKS', NEWSLETTER_TESTS . '/core/mock/com_newsletter');
}

if (!defined('JPATH_LIBRARIES'))
{
	define('JPATH_LIBRARIES', JOOMLA_PATH . '/libraries');
}

// Import the platform.
require_once 'import.php';


// Register the core Joomla test classes.
JLoader::registerPrefix('Test', NEWSLETTER_TESTS . '/core');
JLoader::registerPrefix('NewsletterTest', NEWSLETTER_TESTS . '/core');

//require_once 'PHPUnit/Extensions/Database/Autoload.php';
