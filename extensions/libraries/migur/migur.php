<?php

/**
 * The main file of Migur library. Set environment. Include components.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
define('MIGUR', 1);

// Check if Koowa is active
if (!defined('MIGUR')) {
	JError::raiseWarning(0, JText::_("MIGUR library wasn't found."));
	return;
}

jimport('migur.library.modellist');
jimport('migur.library.view');
jimport('migur.library.table');
jimport('migur.library.model');
