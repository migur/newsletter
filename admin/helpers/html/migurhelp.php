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
	public static function link($category, $name = null, $anchor = null, $version = null, $options = array())
	{
		$text   = JArrayHelper::getValue($options, 'text', '(?)');
		$target = JArrayHelper::getValue($options, 'target', '_blank');
		
		$link   = NewsletterHelperSupport::getResourceUrl($category, $name, $anchor, $version, $options);
		
		return "<a target=\"$target\" href=\"$link\">$text</a>";
	}
}
