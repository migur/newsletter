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
class plgNewsletterGanalytics extends JPlugin
{
	/**
	 * Track each link by Google Analytics
	 *
	 * @param object $caller The object which trigger this event.
	 * @param string $content The content of a letter. Use it as pointer.
	 * @param object $newsletter Newsletter object.
	 *
	 * @return void
	 * @since  12.06
	 */
	function onMigurAfterNewsletterRender(&$content)
	{
		die;
		$content='ololo';
		return;
		$params = $this->params->toArray();
		
		// Find all href='*' or href="*"
		preg_match_all("/((?:href[\s\=\"]+)([^\"]+))|((?:href[\s\=\']+)([^\']+))/", $content, $matches);
		$search = array_unique($matches[1]);
		$withs = array();

		foreach ($search as $item) {
			$sep = (strpos($item, '?') === false)? '?' : '&';
			$withs[] = $item.$sep.$params['goal'];
		}

		$content = str_replace($search, $withs, $content);
	}
}
?>
