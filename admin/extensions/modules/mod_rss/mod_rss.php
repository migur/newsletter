<?php

/**
 * The main logic of the module.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
$news = RssfeedHelper::loadFeed(new JObject(
			array('rssurl' => $params->get('feed_url')))
);

if ($news !== false) {

	echo "<fieldset><legend>" . $news['description'] . "</legend>";
	if (!empty($news['items'])) {
		foreach ($news['items'] as $item) {
			echo "<div>";
			echo '<a href="' . $item['link'] . '" target="_blank">';
			echo $item['title'];
			echo '</a></div>';
		}
	}
	echo "</fieldset>";
}
?>
