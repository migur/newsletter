<?php

/**
 * The main file of Migur library. Set environment. Include components.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
define('MIGUR', 1);

define('MIGURPATH_LIBRARY', JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'migur' . DIRECTORY_SEPARATOR . 'library');

// Check if Koowa is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	JError::raiseWarning(0, JText::_("MIGUR library wasn't found."));
	return;
}

jimport('migur.library.modellist');
jimport('migur.library.view');
jimport('migur.library.table');
jimport('migur.library.model');
