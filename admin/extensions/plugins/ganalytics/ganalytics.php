<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ganalytics
 *
 * @author woody
 */
class ganalytics
{
	/**
	 * Track each link by Google Analytics
	 *
	 * @param string $content - the content of a letter
	 * @param string $uid     - the user subscription key
	 * @param string $newsletterId  - newsletter id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function onafterrender($params, $document)
	{
		if (!$document->trackingGa) {
			return;
		}

		$content = $document->getContent();

		// Find all href='*' or href="*"
		preg_match_all("/((?:href[\s\=\"]+)([^\"]+))|((?:href[\s\=\']+)([^\']+))/", $content, $matches);
		$search = array_unique($matches[1]);
		$withs = array();

		foreach ($search as $item) {
			$sep = (strpos($item, '?') === false)? '?' : '&';
			$withs[] = $item.$sep.$params->goal;
		}

		$content = str_replace($search, $withs, $content);
		$document->setContent($content);

		return true;
	}
}
?>
