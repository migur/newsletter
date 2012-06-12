<?php

if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	JError::raiseWarning(0, JText::_("MIGUR library wasn't found."));
	return;
}

// Defaults section
define('SUBSCRIBER_HTML_DEFAULT', true);
define('SUBSCRIBER_STATE_DEFAULT', true);