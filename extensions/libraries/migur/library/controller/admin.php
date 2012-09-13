<?php

/**
 * The extended version of JModelList.
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
 * Class extends the functionality of JModelList
 *
 * @since   1.0
 * @package Migur.Newsletter
 * 
 * @deprecated since 12.05
 */
if (!class_exists('JControllerFormLegacy')) {
	class JControllerAdminLegacy extends JControllerAdmin {}
}

class MigurControllerAdmin extends JControllerAdminLegacy
{}
