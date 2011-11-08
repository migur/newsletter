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
class HtmlPlaceholderSimple
{
	
	public function render($data, $params = null)
	{
		if ($data === null) {
			$data = '';
			//$data = '{' . (!empty($params['name'])? $params['name'] : 'data') . '}';
		}
		return $data;
	}

	public function getData()
	{

	}

}

?>
