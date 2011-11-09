<?php

/**
 * The main logic of the module.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

JLoader::import('helpers.content', JPATH_COMPONENT_ADMINISTRATOR, '');

// Each link in content at the end should has ABSOLUTE url
// If link or src has relative path (not started with 'http')
// then we complement it with current base url
echo ContentHelper::pathsToAbsolute($params->get('text'));
?>
