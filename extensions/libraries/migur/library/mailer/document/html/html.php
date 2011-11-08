<?php

/**
 * The HTML document type main class
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.application.module.helper');
jimport('joomla.language.language');
jimport('joomla.utilities.simplexml');
jimport('joomla.document.document');
jimport('migur.library.mailer.document');

JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');

//JLoader::register('JDocumentHTML', JPATH_LIBRARIES . DS . 'joomla' . DS . 'document' . DS . 'html' . DS . 'html.php');
//JLoader::register('MigurDocumentRenderer', dirname(__FILE__) . DS . 'renderer.php');
/**
 * Class for HTML document type
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurMailerDocumentHTML extends MigurMailerDocument
{

	/**
	 * Parses the template and populates the buffer
	 *
	 * @param array parameters for fetching the template
	 *
	 * @return void
	 * @since 1.0
	 */
	public function parse()
	{

		$this->parsedTags = array();

		// if request contains the template ID (may be set in previous suction)...
		if (empty($this->renderMode))
			$this->renderMode = 'full';


		if ($this->renderMode == 'raw') {

			// replace all POSITIONs to DIVs
			$this->parsedTags["templateTags"] = array(
				'regexp' => '#<position type="([^"]+)" name="([^"]+)" .* \/>#iU',
				'matches' => array(
					'type' => '',
					'name' => '',
					'attribs' => array('renderMode' => 'schematic',
						'showNames' => !empty($this->showNames)
				))
			);


			// replace all IMAGEs to DIVs only tag. no need to replace the params
			$this->parsedTags["images"] = array(
				'regexp' => '#<(img)([^>]+)\/>#iU',
				'matches' => array(
					'type' => '',
					'name' => '',
					'attribs' => array())
			);

			// Do not parse any placeholder...
		}

		if ($this->renderMode == 'htmlconstructor') {

			// replace all POSITIONs to DIVs with schematic mode
			$this->parsedTags["templateTags"] = array(
				'regexp' => '#<position type="([^"]+)" name="([^"]+)" .* \/>#iU',
				'matches' => array(
					'type' => '',
					'name' => '',
					'attribs' => array(
						'renderMode' => 'schematic',
						'showNames' => !empty($this->showNames)
				))
			);

			// Parse the placeholders...
			$this->parsedTags["placeholders"] = array(
				'regexp' => '#\[([\w\s\.]+)\]#iU',
				'matches' => array(
					'name' => '',
					'type' => 'placeholder',
					'attribs' => array()
				)
			);

			// Override some placeholders
			PlaceholderHelper::setPlaceholder('table_background', null, '#FFFFFF');
			PlaceholderHelper::setPlaceholder('text_color', null, '#000000');
			
			// Don't parse any IMG
		}
		
		
		if ($this->renderMode == 'schematic') {

			// replace all POSITIONs to DIVs with schematic mode
			$this->parsedTags["templateTags"] = array(
				'regexp' => '#<position type="([^"]+)" name="([^"]+)" .* \/>#iU',
				'matches' => array(
					'type' => '',
					'name' => '',
					'attribs' => array(
						'renderMode' => 'schematic',
						'showNames' => !empty($this->showNames)
				))
			);

			// Parse the placeholders...
			$this->parsedTags["placeholders"] = array(
				'regexp' => '#\[([\w\s\.]+)\]#iU',
				'matches' => array(
					'name' => '',
					'type' => 'placeholder',
					'attribs' => array()
				)
			);

			// Don't parse any IMG
		}


		// The default behavior. Full parsing...
		if ($this->renderMode == 'full') {

			// replace all POSITIONs to DIVs with default mode
			$this->parsedTags["templateTags"] = array(
				'regexp' => '#<position type="([^"]+)" name="([^"]+)" .* \/>#iU',
				'matches' => array(
					'type' => '',
					'name' => '',
					'attribs' => array()
				)
			);

			// Parse the placeholders...
			$this->parsedTags["placeholders"] = array(
				'regexp' => '#\[([\w\s\.]+)\]#iU',
				'matches' => array(
					'name' => '',
					'type' => 'placeholder',
					'attribs' => array()
				)
			);

			// Don't parse any IMG
		}


		$this->_parseTemplate();
		return $this->_template->getProperties();
	}

	/**
	 * Load a template file
	 *
	 * @param string	$template	The name of the template
	 * @param string	$filename	The actual filename
	 *
	 * @return string The contents of the template
	 * @since  1.0
	 */
	protected function _loadTemplate($id)
	{
		// Try to find the newsletter by id.
		// Supported both standard and custom

		$model = JModel::getInstance("Template", "NewsletterModel");
		$template = $model->getTemplateBy($id, 'preserve positions!');

		if (!$template) {
			$this->setError($model->getError());
			return false;
		}
		$template->params = $this->_mapParams($template->params);
		PlaceholderHelper::setPlaceholders($template->params);

		return $template;
	}

	/**
	 * Load a renderer
	 *
	 * @access	public
	 * @param	string	The renderer type
	 *
	 * @return	object
	 * @since   1.0
	 */
	function loadRenderer($type)
	{
		$class = 'MigurDocumentHtmlRenderer' . $type;

		if (!class_exists($class)) {
			$path = dirname(__FILE__) . DS . 'renderer' . DS . $type . '.php';
			if (file_exists($path)) {
				require_once $path;
			} else {
				JError::raiseError(500, JText::_('Unable to load renderer class'));
			}
		}

		if (!class_exists($class)) {
			JError::raiseError(500, JText::_('Unable to find the class'));
			return null;
		}

		$instance = new $class($this);
		return $instance;
	}

	/**
	 * Method implements the com_newsletter tracking functionality.
	 * 
	 * @param type $content - the content 
	 * @param type $uid     - subscription key
	 * @param type $newsletterId - newsletter id
	 * @return type 
	 */
	function track(&$content, $uid, $newsletterId)
	{
		$this->_trackLinks($content, $uid, $newsletterId);
		$this->_addTrackingImg($content, $uid, $newsletterId);
		return true;
	}

	/**
	 * Track each link
	 *
	 * @param string $content - the content of a letter
	 * @param string $uid     - the user subscription key
	 * @param string $newsletterId  - newsletter id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function _trackLinks(&$content, $uid, $newsletterId)
	{
		// Find all hrefs
		preg_match_all('/href[\s\=\"\']+([^\"\']+)[\"\']/', $content, $matches);
		$fullhrefs = array_unique($matches[0]);
		$urls = array_unique($matches[1]);
		
		$withs = array();
		if (!empty($urls)) {
			foreach($urls as $i => $val) {

				$withs[] = str_replace(
					$val,
					JRoute::_(
						'index.php?option=com_newsletter&task=newsletter.track&format=json&action=clicked&uid=' . $uid . '&nid=' . $newsletterId .
						'&link=' . urlencode(base64_encode($val)), FALSE, 2
					),
					$fullhrefs[$i]
				);	
			}

			$content = str_replace($fullhrefs, $withs, $content);
		}
			
		return true;
	}

	/**
	 * The method to add the Track image to end of email's body
	 *
	 * @param string $content - the content of a letter
	 * @param string $uid     - the user subscription key
	 * @param string $newsletterId  - newsletter id
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function _addTrackingImg(&$content, $uid, $newsletterId)
	{
		$lnk = JRoute::_(
				'index.php?option=com_newsletter&task=newsletter.track&format=json&action=opened&uid=' . $uid . '&nid=' . $newsletterId, FALSE, 2
		);
		$content .= '<img src="' . $lnk . '" style="height:1px;width:1px;" width="1" height="1" />';
		return true;
	}

	/**
	 * Method to trigger the events of Com_newsletter component's plugins
	 * 
	 * @param string $event - name of the event
	 * 
	 * @return boolean
	 * @since  1.0
	 */
	function triggerEvent($event)
	{

		$plugins = MigurPluginHelper::getUsedInNewsletter($this->getNewsletterId());

		foreach ($plugins as $plugin) {
			MigurPluginHelper::trigger($plugin->extension, $event, $plugin->params, $this);
		}
		
		return true;
	}

}
