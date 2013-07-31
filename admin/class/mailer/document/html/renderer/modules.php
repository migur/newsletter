<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

/**
 * Class of HTML modules container renderer
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurDocumentHtmlRendererModules extends JDocumentRenderer
{
	/**
	 * Renders multiple modules script and returns the results as a string
	 *
	 * @param	string	$name		The position of the modules to render
	 * @param	array	$params		Associative array of values
	 *
	 * @return	string	The output of the script
	 * @since   1.0
	 */
	public function render($position, $params = array(), $content = null)
	{
		$buffer = '';
		$pre = '';
		$post = '';
		// The schmatic view of position areas
		if (!empty($params['renderMode']) && $params['renderMode'] == 'schematic') {
			$pre = '<div name="' . $position . '" class="modules ' . $position . '">';

			// Switch the name of the droppable area
			if (!empty($params['showNames'])) {
				$pre .= '<div style="position:relative;">' . $position .  '</div>';
			}
			$post = '</div>';
		}

		$modules = NewsletterHelperModule::getModules($position);
		
		// Sort the modules by ordering
		$total = count($modules);
		for ($i = 0; $i < $total; $i++) {
			for ($k = $i+1; $k < $total; $k++) {
				
				if ($modules[$k]->ordering < $modules[$i]->ordering) {
					$buff = $modules[$k];
					$modules[$k] = $modules[$i];
					$modules[$i] = $buff;
				}
			}	
		}
		
		foreach ($modules as $mod) {

			// The default behavior
			$renderer = $this->_doc->loadRenderer('module', $mod->native);

			$buffer .= $renderer->render($mod, $params, $content);
		}
		return $pre . $buffer . $post;
	}

}