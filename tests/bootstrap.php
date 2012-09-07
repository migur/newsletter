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


define('NEWSLETTER_SRC', dirname(dirname(__FILE__)));
define('NEWSLETTER_TESTS', dirname(__FILE__));
define('JPATH_NEWSLETTER_MOCKS', NEWSLETTER_TESTS . '/core/mock/com_newsletter');

if (!defined('JPATH_ROOT'))				define('JPATH_ROOT', JOOMLA_PATH);
if (!defined('JPATH_BASE'))				define('JPATH_BASE', NEWSLETTER_TESTS . '/tmp');


/* From defines.php */
if (!defined('JPATH_SITE'))				define('JPATH_SITE', JOOMLA_PATH);
if (!defined('JPATH_ADMINISTRATOR'))	define('JPATH_ADMINISTRATOR', JOOMLA_PATH . '/administrator');
if (!defined('JPATH_CONFIGURATION'))	define('JPATH_CONFIGURATION', JOOMLA_PATH);
if (!defined('JPATH_LIBRARIES'))		define('JPATH_LIBRARIES', JOOMLA_PATH . '/libraries');
if (!defined('JPATH_PLUGINS'))			define('JPATH_PLUGINS', JOOMLA_PATH . '/plugins');
if (!defined('JPATH_INSTALLATION'))		define('JPATH_INSTALLATION',	JPATH_ROOT . '/installation');
if (!defined('JPATH_THEMES'))			define('JPATH_THEMES', JOOMLA_PATH . '/themes');
if (!defined('JPATH_CACHE'))			define('JPATH_CACHE', JOOMLA_PATH . '/cache');
if (!defined('JPATH_MANIFESTS'))		define('JPATH_MANIFESTS', JOOMLA_PATH . '/manifests');
/* From defines.php */

define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_newsletter');

if (!defined('JPATH_PLATFORM'))			define('JPATH_PLATFORM', JOOMLA_PATH . '/libraries');


// Import the platform.
require_once 'import.php';

require_once JOOMLA_PATH . '/configuration.php';

// Register the core Joomla test classes.
JLoader::registerPrefix('Test', NEWSLETTER_TESTS . '/core');
JLoader::registerPrefix('NewsletterTest', NEWSLETTER_TESTS . '/core');

//require_once 'PHPUnit/Extensions/Database/Autoload.php';
