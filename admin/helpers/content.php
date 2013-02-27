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

class NewsletterHelperContent
{

   /**
	 * Each link in content at the end should has ABSOLUTE url
	 * If link or src has relative path (not started with 'http' or another schema)
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
		// Find all ahrefs
		$pat =
			'(?:(?:(?:href|src)\s*\=\s*\"\s*)([^\"]+))|'. // Double quoted case
			'(?:(?:(?:href|src)\s*\=\s*\'\s*)([^\']+))|'. // Single quoted case
			'(?:(?:(?:href|src)\s*\=\s*)([^\s\>\<]+))' // Case without quotes
		;

		// Make it multiline caseinsensetive ungreedy
		preg_match_all("/$pat/im", $content, $matches);
		
		// Create unique pattern-url pairs for substitution
		$urls = array();
		$patterns = $matches[0];
		for($i=0; $i < count($patterns); $i++) {
			
			// Make it unique!
			if (array_key_exists($patterns[$i], $urls)) {
				continue;
			}	

			// Try to get url
			$url = null;
			if (!empty($matches[1][$i])) $url = $matches[1][$i];
			if (!empty($matches[2][$i])) $url = $matches[2][$i];
			if (!empty($matches[3][$i])) $url = $matches[3][$i];

			// If there is no extracted url then just do not modify it
			if (!$url) continue;
			
			// Let's determine if this is a relative link
			// If we find the scheme then this is not a relative link.
			if (empty($url) || preg_match('/^[a-z0-9\.\-]{2,}\:/imU', $url) == 1) {
				continue;
			}

			// But if this link is relative then repair it!
			$fixedUrl = $url;
			
			$pathprefix = JUri::base(true);

			// remove the path prefix from the begin of item 
			// if it presents 
			if (!empty($pathprefix) && strpos($fixedUrl, $pathprefix) === 0) {
				$fixedUrl = substr($fixedUrl, strlen($pathprefix));
			}

			// remove the '/' from the begin of item
			if (strpos($fixedUrl, '/') === 0 && strlen($fixedUrl) > 1) {
				$fixedUrl = substr($fixedUrl, 1);
			}

			// Compile link
			$fixedUrl = JUri::root() . $fixedUrl;
			
			// If we have &amp; from somewhere then fix it
			$fixedUrl = str_replace('&amp;', '&', $fixedUrl);

			$urls[$patterns[$i]] = str_replace($url, $fixedUrl, $patterns[$i]);
		}
		
		return str_replace(array_keys($urls), array_values($urls), $content);
	}
	
	public static function repairHTML($html)
	{
		if (class_exists('DOMDocument')) {
			
			$html = utf8_decode($html);
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			$html = $doc->saveHTML();
			$html = utf8_encode($html);

			$html = preg_replace(array('/^<!DOCTYPE.*>\s*<html>\s*<body>/', '/<\/body>\s*<\/html>$/im'), array('', ''), $html);
		}
		
		return $html;
	}

}

/**
 * Legacy support for class name
 */
class ContentHelper extends NewsletterHelperContent 
{}