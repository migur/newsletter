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
 * NewsletterHelperLog::addDebug('Newsletter bebug', 'test', array('name1' => 'value1'));
 * NewsletterHelperLog::addMessage('Newsletter message', 'test', array('name2' => 'value2'));
 * NewsletterHelperLog::addError('Newsletter error', 'test', array('name3' => 'value3'));
 * JError::raiseError(501, 'Joomla error');
 * JError::raiseNotice(501, 'notice'); // onto screen
 *
 * @since		1.0
 */
class NewsletterHelperSupport
{
    //static public $resourceUrl = 'administrator/index.php?option=com_newsletter&view=support';

    static public $resourceUrlRemote = COM_NEWSLETTER_SUPPORT_REMOTE_URL;

	public static function getResourceUrl($route, $options = array())
	{
		$version = NewsletterHelperNewsletter::getVersion();

		if (strpos($route, 'com-newsletter') !== 0) {
			$route = 'com-newsletter/' . trim($route, '/');
		}

		return self::$resourceUrlRemote . '/' . trim($route, '/') . '/' . self::_cleanSegment($version);
	}

	protected static function _cleanSegment($string)
	{
		return trim(preg_replace('/[^0-9a-z\-]+/','-', $string), '/');
	}
}
