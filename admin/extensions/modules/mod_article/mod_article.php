<?php

/**
 * The main logic of the module.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

$id = (int) $params->get('id');

$db = JFactory::getDbo();
$query = $db->getQuery(true);

$query->select('*');
$query->from('#__content');
$query->where('id = ' . $db->quote($id));
$db->setQuery($query);
$art = $db->loadObject();

if (!empty($art)) {

	$params->set('title', $art->title);

	$textMode = (int) $params->get('text_mode', 0);

	// If READMORE
	if ($textMode == 2) {
		$data = $art->introtext;
	} else {
		$data = $art->introtext . $art->fulltext;
	}

	if ($params->get('strip_tags', 0) != 0) {
		$data = strip_tags($data, '<br><p>');
	}

	if ($textMode == 0) {
		$data = @substr($data, 0, strpos($data, ' ', (int) $params->get('text_amount', '200')));
	}	
	
	$data = NewsletterHelperContent::repairHTML($data);
	
	echo $data;
	
	if ($params->get('show_articlelink', 1) == 1) {
		
		$link = JUri::root() . '?option=com_content&view=article&id=' . $id;
		$linkTitle = ($params->get('articlelink_text') != '')? JText::_($params->get('articlelink_text')) : '...';
		$linkTitle = htmlspecialchars($linkTitle);
		echo '<a class="readmore" href="' . $link . '">' . $linkTitle . '</a>';
	}
}
