<?php

/**
 * The extended version of JController.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

/**
 * Class extends the functionality of JController
 *
 * @since   1.0
 * @package Migur.Newsletter
 * 
 * @deprecated since 13.06
 */
if (!class_exists('JControllerLegacy')) {
	class JControllerLegacy extends JController {}
}

class MigurController extends JControllerLegacy {}

