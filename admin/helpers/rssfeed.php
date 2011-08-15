<?php
/**
 * The RSS feed helper. Allow to get the content of an RSS.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */  
 
 // No direct access.
defined('_JEXEC') or die;

/**
 * Class for the RSS helper
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
 class RssfeedHelper
{

	/**
	 * Prepare params and return the result
	 *
	 * @param  object $params - the params of RSS
	 *
	 * @return array - the array of RSS items
	 * @since  1.0
	 */
	public static function loadFeed($params)
	{

		// module params
		$filter = JFilterInput::getInstance();

		//  get RSS parsed object
		$options = array();
		$options['rssUrl'] = $params->rssurl;
		$options['cache_time'] = 0;

		$parser = @JFactory::getXMLparser('RSS', $options);
		if ($parser) {

			return RssfeedHelper::_parse($parser, $params);

		} else {
			return false;
		}
	}

	/**
	 * Gets the RSS content
	 *
	 * @param  XMLparser $rssDoc - the instance of the parser
	 * @param  object    $params - the params of RSS
	 *
	 * @return array - the array of RSS items
	 * @since  1.0
	 */
	public static function _parse($rssDoc, $params)
	{
		// module params
		$rssurl = $params->get('rssurl', '');
		$rssitems = $params->get('rssitems', 5);
		$rssdesc = $params->get('rssdesc', 1);
		$rssimage = $params->get('rssimage', 1);
		$rssitemdesc = $params->get('rssitemdesc', 1);
		$words = $params->def('word_count', 0);
		$rsstitle = $params->get('rsstitle', 1);
		$rssrtl = $params->get('rssrtl', 0);
		$moduleclass_sfx = $params->get('moduleclass_sfx', '');

		$filter = JFilterInput::getInstance();


		if ($rssDoc != false) {
			// channel header and link
			$channel['title'] = $filter->clean($rssDoc->get_title());
			$channel['link'] = $filter->clean($rssDoc->get_link());
			$channel['description'] = $filter->clean($rssDoc->get_description());

			// channel image if exists
			$image['url'] = $rssDoc->get_image_url();
			$image['title'] = $rssDoc->get_image_title();

			//image handling
			$iUrl = isset($image['url']) ? $image['url'] : null;
			$iTitle = isset($image['title']) ? $image['title'] : null;

			// items
			$items = $rssDoc->get_items();

			// feed elements
			$items = array_slice($items, 0, $rssitems);

			$res = $channel;
			// feed description
			$actualItems = count($items);
			$setItems = $rssitems;

			if ($setItems > $actualItems) {
				$totalItems = $actualItems;
			} else {
				$totalItems = $setItems;
			}
			for ($j = 0; $j < $totalItems; $j++) {
				$arr['link'] = $items[$j]->get_link();
				$arr['title'] = $items[$j]->get_title();
				$arr['description'] = $items[$j]->get_description();

				$res['items'][] = $arr;
			}

			return $res;
		}
	}
}
