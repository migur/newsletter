<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of list
 *
 * @author woody
 */
class PlainPlaceholderLink
{

	public function render($data, $params = null)
	{
		if (empty($data)) {
			$data = '';
		}
		
		// 2 - to create non-https FULL link (with server name)
		$link = JRoute::_($data, false, 2);
		
		// Workaround for placedin placeholders
		$link = preg_replace('/\%20/u', ' ', $link);
		
		return $link;
	}

	public function getData()
	{
	}
}

?>
