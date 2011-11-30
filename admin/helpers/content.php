<?php

/**
 * The data managing helper.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class ContentHelper
{

       /**
        * Each link in content at the end should has ABSOLUTE url
        * If link or src has relative path (not started with 'http')
        * then we complemet it with current base url
        *
        * @param string $content to handle
        *
        * @return string result content with repaired paths
        *
        * @since 1.0.2
        */
    	public static function pathsToAbsolute($content)
	{
		// Gets all links (href and src attributes has been parsed)
		preg_match_all("/(?:href[\s\=\"\']+|src[\s\=\"\']+)([^\"\']+)/", $content, $matches);

		$withs = array();
		for($i=0; $i < count($matches[0]); $i++) {
                        $item = $matches[1][$i];
			// if this link is relative then repair it!
			if (!empty($item) && substr($item, 0, 4) != 'http') {

				$pathprefix = JUri::base(true);

				// remove the path prefix from the begin of item
				if (!empty($pathprefix) && strpos($item, $pathprefix) === 0) {
					$item = substr($item, strlen($pathprefix));
				}

				// remove the '/' from the begin of item
				if ($item[0] == '/' && sizeof($item) > 1) {
					$item = substr($item, 1);
				}

				// Compile link
				$item = JUri::root() . $item;
				$item = str_replace('&amp;', '&', $item);

			}

			$withs[] = str_replace($matches[1][$i], $item, $matches[0][$i]);
		}

		return str_replace($matches[0], $withs, $content);
	}

}
