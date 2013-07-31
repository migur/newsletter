<?php

/**
 * The main logic of the module.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
$news = NewsletterHelperRssfeed::loadFeed(new JObject(
			array('rssurl' => $params->get('feed_url')))
);

if ($news !== false) {

	//htmlspecialchars($news['description'], ENT_QUOTES | ENT_DISALLOWED | ENT_HTML401, 'UTF-8') 
	echo "<fieldset>\n<legend>" . $news['description'] . "</legend>\n";
	if (!empty($news['items'])) {
		foreach ($news['items'] as $item) {
			echo "<div>\n";
			echo '<a href="' . $item['link'] . '" target="_blank">';
			echo $item['title'];
			echo "</a>\n</div>";
		}
	}
	echo "</fieldset>";
}
?>
