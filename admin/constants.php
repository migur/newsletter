<?php
if (!defined('MIGUR_COM_NEWSLETTER')) {
	
	// Environment constants
	define('COM_NEWSLETTER_PATH_ADMIN', JPATH_ROOT
		.DIRECTORY_SEPARATOR.'administrator'
		.DIRECTORY_SEPARATOR.'components'
		.DIRECTORY_SEPARATOR.'com_newsletter'
	);

	define('COM_NEWSLETTER_PATH_SITE', JPATH_ROOT
		.DIRECTORY_SEPARATOR.'components'
		.DIRECTORY_SEPARATOR.'com_newsletter'
	);

	// Defaults section
	define('SUBSCRIBER_HTML_DEFAULT', true);
	define('SUBSCRIBER_STATE_DEFAULT', true);
	
	// At last define the flag for com_newsletter component
	define('MIGUR_COM_NEWSLETTER', true);
}
