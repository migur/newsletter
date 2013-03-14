<?php

/**
 * The grid helper. Contain the methods to create the controls for grid.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

abstract class JHtmlLayout
{
	public static function wrapper($sidebar = null) 
	{
		if ($sidebar == null) {
			$sidebar = JHtmlSidebar::render();
		}
		return 
			'<div>'.
			'<div id="j-sidebar-container" class="span2">'.$sidebar.'</div>'.
			'<div id="j-main-container" class="span10">';
	}	

	public static function wrapperEnd() 
	{
		return '</div></div>';
	}	
}
