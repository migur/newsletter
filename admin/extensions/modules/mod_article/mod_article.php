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

	$data = $art->introtext;
	if (strlen($data) > 200) {
		$data = substr($data, 0, strpos($data, ' ', 200));
	}
	$link = JUri::root() . '?option=com_content&view=article&id=' . $id;

	echo $data . '<a href="' . $link . '"><b>&nbsp;&nbsp;.&nbsp;.&nbsp;.&nbsp;</b></a>';
}	
