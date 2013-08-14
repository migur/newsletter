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

abstract class JHtmlMigurhelp
{
	public static function link($route, $options = array())
	{
		$text   = JText::_(JArrayHelper::getValue($options, 'text', '(?)'), true);
		$width =  JArrayHelper::getValue($options, 'width', '980');
		$height = JArrayHelper::getValue($options, 'height', '600');

		$link   = NewsletterHelperSupport::getResourceUrl($route, $options);

		$task = "popupWindow('$link', '$text', $width, $height, 1)";

		return "<a href=\"#\" onclick=\"{$task}; return false;\">{$text}</a>";
	}
}
