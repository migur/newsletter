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
class HtmlPlaceholderList
{

	public function render($data, $params = null)
	{
		if (is_array($data) && !empty($data)) {
			$res = '<ul>';
			foreach ($data as $item) {
				$res .= '<li>' . htmlspecialchars($item) . '</li>';
			}
			return $res . '</ul>';
		} else {
			return "(list is empty)";
		}
	}

	public function getData()
	{
		
	}

}

?>
