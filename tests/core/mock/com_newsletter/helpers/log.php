<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Log
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Inspector classes for the JLog package.
 */

require_once JPATH_NS_COMPONENT.'/admin/helpers/log.php';
/**
 * @package		Joomla.UnitTest
 * @subpackage  Log
 */
class TestMockLogHelper extends NewsletterHelperLog
{
    public static $logs = array();
    
	/**
	 * Log a entry into log.
	 * 
	 * @param string Message
	 * @param string File name, usae current date otherwise
	 * @param boolean Use to force the logging
	 */ 
	static public function addEntry($priority, $msg, $category = null, $data = null) 
	{
        self::$logs[] = array(
            'priority' => $priority,
            'msg'      => $msg,
            'category' => $category,
            'data'     => $data
        );
        
		return true;
	}
    
	static public function addMessage($msg, $category = null, $data = null) 
	{
        return parent::addMessage($msg, $category = null, $data = null);
    }
    
}
