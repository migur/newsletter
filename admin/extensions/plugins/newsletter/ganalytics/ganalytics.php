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
		$goal = $this->params->get('goal');
		
		if (empty($goal)) {
			return;
		}
		
		$allowedSchemes = array('http', 'https');
		
		// Find all ahrefs
		$pat =
			'(?:(?:href\s*\=\s*\"\s*)([^\"]+)\")|'. // Double quoted case
			'(?:(?:href\s*\=\s*\'\s*)([^\']+)\')|'. // Single quoted case
			'(?:(?:href\s*\=\s*)([^\s\"\'\<]+))' // Case without quotes
		;

		// Make it multiline caseinsensetive
		$matches = array();
		preg_match_all("/$pat/im", $content, $matches);
		
		// Create unique pattern-url pairs for substitution
		$urls = array();
		$patterns = $matches[0];
		for($i=0; $i < count($patterns); $i++) {
			
			// Make it unique!
			if (array_key_exists($patterns[$i], $urls)) {
				continue;
			}	

			// Try to get url
			$url = null;
			
			do {
				if (!empty($matches[1][$i])) { $url = $matches[1][$i]; break; }
				if (!empty($matches[2][$i])) { $url = $matches[2][$i]; break; }
				if (!empty($matches[3][$i])) { $url = $matches[3][$i]; break; }
				break;
			} while(false);	

			// If there is no extracted url then just do not modify it
			if (!$url) continue;

			// Check if scheme of url is allowed to be tracked
			$allowed = false;
			foreach($allowedSchemes as $scheme) {
				if (strpos($url, $scheme) === 0) {
					$allowed = true;
					break;
				}	
			}	

			// If url is not allowed then just skip it
			if (!$allowed) {
				continue;
			}

			
			$sep = (strpos($url, '?') === false)? '?' : '&';
			$urls[$patterns[$i]] = str_replace($url, $url.$sep.$goal, $patterns[$i]);
		}
		
		// Finaly replace patterns with allowed processed modifications
		$content = str_replace(array_keys($urls), array_values($urls), $content);
	}
}
?>
