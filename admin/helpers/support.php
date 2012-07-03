<?php

/**
 * The newsltter main component helper
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Content component helper.
 * 
 * To test:
 * LogHelper::addDebug('Newsletter bebug', 'test', array('name1' => 'value1'));
 * LogHelper::addMessage('Newsletter message', 'test', array('name2' => 'value2'));
 * LogHelper::addError('Newsletter error', 'test', array('name3' => 'value3'));
 * JError::raiseError(501, 'Joomla error');
 * JError::raiseNotice(501, 'notice'); // onto screen
 *
 * @since		1.0
 */
class SupportHelper
{
	static public $resourceUrl = 'administrator/index.php?option=com_newsletter&view=support';
	
	public static function getHelpUrl($category, $name, $version = null, $options = array())
	{
		$resourceUrl = '';
		
		if (!empty($category)) {
			$resourceUrl .= '&category='.$category;
		}	

		if (!empty($name)) {
			$resourceUrl .= '&name='.$name;
		}	
		
		if (!empty($version)) {
			$resourceUrl .= '&version='.$category;
		}	

		// Add some params (dafault or provided)
		$params = empty($options['params'])? array() : (array) $options['params'];
		
		if (empty($params['tmpl'])) {
			$params['tmpl'] = 'component';
		}	
		
		foreach($params as $name => $val) {
			$resourceUrl .= '&'.$name.'='.$val;
		}
		
		
		return JUri::root(). self::$resourceUrl . $resourceUrl;
	}	
	
	
	public static function getCategoryItems($category)
	{
		switch($category) {
			case 'list':
				return array(
					'import' => array(
						'url' => self::getHelpUrl('list', 'import'),
						'title'       => 'Importing into list - Help page'),
					
					'exclude' => array(
						'url' => self::getHelpUrl('list', 'exclude'),
						'title'       => 'Excluding from list - Help page')

				);
				
			default: return array();	
		}
	}
}