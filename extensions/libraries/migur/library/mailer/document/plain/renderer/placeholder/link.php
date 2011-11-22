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
		return $data;
	}

	public function getData()
	{
	}
}

?>
