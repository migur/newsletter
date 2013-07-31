<?php
/**
 * Prepares a minimalist framework for unit testing.
 */

 // Set flag that this is a parent file
define('_JEXEC', 1);

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

// Maximise error reporting.
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);

define('NEWSLETTER_SRC',			dirname(dirname(__FILE__)));
define('NEWSLETTER_TESTS',			dirname(__FILE__));
define('JPATH_NEWSLETTER_MOCKS',	NEWSLETTER_TESTS . '/core/mock/com_newsletter');

define('JOOMLA_PATH',				dirname(dirname(dirname(__FILE__))) . '/joomla');
define('JPATH_BASE',				NEWSLETTER_TESTS . '/tmp');

require_once NEWSLETTER_TESTS.'/includes/defines.php';
require_once NEWSLETTER_TESTS.'/includes/framework.php';

define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_newsletter');
define('JPATH_COMPONENT',				JPATH_SITE . '/components/com_newsletter');

// Register the core Joomla test classes.
JLoader::registerPrefix('Test', NEWSLETTER_TESTS . '/core');
JLoader::registerPrefix('NewsletterTest', NEWSLETTER_TESTS . '/core');
