<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of HelloWorld component
 */
class pkg_newsletterInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @param  object - parent
	 *
	 * @return boolean
	 * @since  12.06
	 */
	public function preflight($route, $parent)
	{
		// Its needed to install component last. After library.
		
		// Let's reverse the order of extensions in the install flow
		$manifest = $parent->manifest;
		
		$children = array_reverse($manifest->xpath('//extension/files/file'));

		$cloneds = array();
		foreach($children as &$item) {
			$cloneds[] = clone $item;
		}	

		unset($manifest->files->file);
		
		foreach($cloneds as $child) {
			
			$new = $manifest->files->addChild('file', (string) $child);
			foreach($child->attributes() as $name => $val) {
				$new->addAttribute($name, $val);
			}	
			
		}
		
		return true;
	}	
}
