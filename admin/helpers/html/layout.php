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
	
	public static function controlgroup($label, $controls, $options = array(), $renderLabelWrapper = false) 
	{
		$label = preg_replace('/(class\=\"[^\"]+)(\")/mui', "$1 control-label $2", $label);
		
		$controls = (array) $controls;
		
		
		return '<div class="control-group">' .
					($renderLabelWrapper? '<label class="control-label">' : '').
						JText::_($label).
					($renderLabelWrapper? '</label>':'').
					'<div class="controls">'.implode("\n", $controls).'</div>'.
				'</div>';
	}	
	
}
