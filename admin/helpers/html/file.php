<?php

/**
 * The file helper. Contain the methods to manage and format the data of files
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

abstract class JHtmlFile
{

	/**
	 * The formatter for file size
	 *
	 * @param string - data
	 * @param string - format string
	 *
	 * @return string
	 * @since 1.0
	 */
	public static function size($value, $format)
	{
		$value = intval($value);
		$res = $value;
		$suffix = 'bytes';

		switch ($format) {
			case 'kb/mb':
			default:
				$res = $value / 1024;
				$suffix = 'Kb';
				if ($res > 1024) {
					$res = $res / 1024;
					$suffix = 'Mb';
				}
		}

		return strval(floor($res * 10) / 10) . ' ' . $suffix;
	}

}
