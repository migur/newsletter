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
class PlainPlaceholderList
{

	public function render($data, $params = null)
	{
		if (is_array($data) && !empty($data)) {
			$res = '';
			foreach ($data as $item) {
				$res .= " - " . $item . "\n";
			}
			return $res;
		} else {
			return "(list is empty)";
		}
	}

	public function getData()
	{
		
	}

}

?>
