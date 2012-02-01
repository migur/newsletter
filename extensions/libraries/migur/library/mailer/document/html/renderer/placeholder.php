<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class for HTML placeholder renderer
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurDocumentHtmlRendererPlaceholder extends JDocumentRenderer
{

	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param	string $name	The name of the module to render
	 * @param	array $attribs	Associative array of values
	 *
	 * @return	string			The output of the script
	 * @since   1.0
	 */
	public function render($name, $attribs = array(), $content = null)
	{
		return PlaceholderHelper::render($name, 'html');
	}
}
