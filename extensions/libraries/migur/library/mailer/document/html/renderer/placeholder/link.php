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
class HtmlPlaceholderLink
{

	public function render($data, $params = null)
	{
		if (empty($data)) {
			$data = '#';
		}
		return '<a href="'.$data.'">' . htmlspecialchars($params['name']) . '</a>';
	}

	public function getData()
	{
	}
}

?>
