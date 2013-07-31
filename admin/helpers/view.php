<?php

/**
 * View helper that wraps some routines we need in view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

class NewsletterHelperView
{
	/**
	 * Wraps JFactory::getDocument()->addstylesheet(JURI::root().$url); with adding 
	 * ?ver=[component_current_version] in the url.
	 * 
	 * @param type $url
	 * 
	 * @return void
	 * 
	 * @since 13.06
	 */
	
	static public function addStyleSheet($url, $type = 'text/css', $media = null, $attribs = array(), $addRoot = true)
	{
		$ver = NewsletterHelperNewsletter::getVersion();
		$url = ($addRoot? JUri::root() . trim($url, '/') : $url) . '?ver=' . urlencode($ver);
		JFactory::getDocument()->addStyleSheet($url, $type, $media, $attribs);
	}

	static public function addScript($url, $type = "text/javascript", $defer = false, $async = false, $addRoot = true) 
	{
		$ver = NewsletterHelperNewsletter::getVersion();
		$url = ($addRoot? JUri::root() . trim($url, '/') : $url) . '?ver=' . urlencode($ver);
		JFactory::getDocument()->addScript($url, $type, $defer, $async);
	}
}
