<?php

/**
 * The main logic of the module.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

$img = $params->get('img');

$w = $params->get('img_width', '');
$width = (empty($w) || strpos($w, 'auto'))? '' : 
	' width="' . (is_numeric($w)? ($w.'px') : $w) .'" ';

$h = $params->get('img_height', '');
$height = (empty($h) || strpos($h, 'auto'))? '' : 
	' height="' . (is_numeric($h)? ($h.'px') : $h) .'" ';

$a = $params->get('img_alt', '');
$alt = (!empty($a))? ' alt="'.JText::_($a).'" ' : '';

$linkable = $params->get('img_linkable', '0') == '1';

if ($linkable) {
	$t = $params->get('img_link_target', '1');
	$target = ($t==0)? '' : ' target="_blank" ';

	$u = $params->get('img_link_url', '');
	$url = (!empty($u))? $u : $img;
	
	echo '<a href="'.$url.'"'.$target.'>'."\n";
}

echo '<img src="'.$img.'"'.$width.$height.$alt.'/>'."\n";

if ($linkable) {
	echo '</a>';
}