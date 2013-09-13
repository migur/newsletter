<?php

/**
 * The common document class. Contain common methods.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

//Register the renderer class with the loader
//jimport('migur.library.mailer');
jimport('joomla.document.renderer');
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.plugin', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class for common document
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterClassMailerDocument extends JDocument
{

	protected $_template;
	protected $_letter;
	public $directory;
	public $_parsed;
	public $parsedTags;
	public $_caching;
	public $useRawUrls;

	// Hotfix. Need to remove when we remove tracking from here.
	public $dispatcher;

	public function __construct($params = array())
	{
		// Hotfix. Need to remove when we remove tracking from here.
		$this->dispatcher = !empty($params['dispatcher'])? $params['dispatcher'] : JDispatcher::getInstance();
		$this->init($params);
	}

	/**
	 * Init the object data
	 *
	 * @param  array $params - the configuration parameters
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function init($params)
	{
		$this->_caching = false;
		$this->_template = null;
		$this->_letter = null;
		$this->_parsed = null;
		$this->directory = null;
		$this->parsedTags = null;
		$this->renderMode = null;
		$this->tracking   = null;
		$this->useRawUrls = false;

		// reset all previous rendererd data for modules or placeholders
		parent::$_buffer = array();

		$this->tracking   = isset($params['tracking'])? (bool)$params['tracking'] : true;

		$this->directory = !empty($params['directory']) ?
			$params['directory'] : JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR . 'templates';

		$this->renderMode = !empty($params['renderMode']) ?
			$params['renderMode'] : 'full';

		$this->showNames = !empty($params['showNames']);

		$this->useRawUrls = !empty($params['useRawUrls']);

		// if we already get the template then work with it.
		if (!empty($params['template'])) {
			$this->_template = $params['template'];
			return true;
		}


		// if request contains the letter ID...
		if (!empty($params['newsletter_id'])) {
			$this->_letter = $this->loadLetter($params['newsletter_id']);
			$this->_template = $this->_loadTemplate($this->_letter->t_style_id);
			return;
		}

		// And finaly try to find the template.
		if (!empty($params['t_style_id'])) {
			$this->_template = $this->_loadTemplate($params['t_style_id']);
			return true;
		}

		$this->setError('The PLAIN is not allowed because the NEWSLETTER is not loaded');
		return false;
	}

	/**
	 * Returns the global JDocument object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param  type $type The document type to instantiate
	 *
	 * @return object  The document object.
	 * @since  1.0
	 */
	public static function getInstance($type = 'html', $attributes = array())
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($type, $attributes));
		if (empty($instances[$signature])) {
			$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $type . '.php';
			$ntype = null;

			// Check if the document type exists
			if (!file_exists($path)) {
				// Default to the raw format
				$ntype = $type;
				$type = 'raw';
			}

			// Determine the path and class
			$class = 'NewsletterClassMailerDocument' . $type;
			if (!class_exists($class)) {
				$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $type . '.php';
				if (file_exists($path)) {
					require_once $path;
				} else {
					// TODO deprecated since 12.1 Use PHP Exception
					JError::raiseError(500, JText::_('JLIB_DOCUMENT_ERROR_UNABLE_LOAD_DOC_CLASS'));
				}
			}

			$instance = new $class($attributes);
			$instances[$signature] = &$instance;

			if (!is_null($ntype)) {
				// Set the type to the Document type originally requested
				$instance->setType($ntype);
			}
		}

		return $instances[$signature];
	}

	/**
	 * Returns the global JDocument object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param  type $type The document type to instantiate
	 *
	 * @return object  The document object.
	 * @since  1.0
	 */
	public static function factory($type = 'html', $attributes = array())
	{
		$signature = serialize(array($type, $attributes));

		// Determine the path and class
		$class = 'NewsletterClassMailerDocument' . $type;
		if (!class_exists($class)) {
			$path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $type . '.php';
			if (file_exists($path)) {
				require_once $path;
			} else {
				// TODO deprecated since 12.1 Use PHP Exception
				JError::raiseError(500, JText::_('JLIB_DOCUMENT_ERROR_UNABLE_LOAD_DOC_CLASS'));
			}
		}

		return  new $class($attributes);
	}

	/**
	 * Get the template
	 *
	 * @return	string	The template name
	 * @since	1.0
	 */
	public function loadLetter($id = false)
	{
		$letter = NewsletterHelperMail::loadLetter($id);

		// set the letter id for the Helper
		NewsletterHelperModule::$itemId = $letter->newsletter_id;
		NewsletterHelperModule::$clean = null;

		return $letter;
	}

	/**
	 * Parse a document template
	 *
	 * @return The parsed contents of the template
	 * @since  1.0
	 */
	protected function _parseTemplate()
	{
		if (empty($this->_template)) {
			throw new Exception('ParseTemplate: template entity is empty');
		}

		$replaces = array();
		foreach ($this->parsedTags as $name => $val) {
			$matches = array();
			$replaces[$name] = array();
			if (preg_match_all($val['regexp'], $this->_template->content, $matches)) {
				if (!empty($matches)) {
					foreach ($matches as $idx => $match) {
						$matches [$idx] = array_reverse($match);
					}

					$count = count($matches[1]);
					for ($i = 0; $i < $count; $i++) {
						$k = 1;
						$res = array();
						foreach ($val['matches'] as $itemName => $item) {

							if (isset($matches[$k])) {

								if ($itemName == 'attribs') {
									$val['matches'][$itemName] = JUtility::parseAttributes($matches[$k][$i]);
								} else {
									$val['matches'][$itemName] = $matches[$k][$i];
								}
							}
							$replaces[$name][$matches[0][$i]] = $val['matches'];
							$k++;
						}
					}
				}
			}
		}

		$this->_parsed = $replaces;

		return true;
	}

	/**
	 * Outputs the template to the browser.
	 *
	 * @param boolean	$cache		If true, cache the output
	 * @param array		$params		Associative array of attributes
	 *
	 * @return The rendered data
	 * @since  1.0
	 */
	public function render($caching = false, $params = array())
	{
		try {

			// Load newseltter language
			JFactory::getLanguage()->load('com_newsletter_modules', JPATH_ADMINISTRATOR);

			// Set the mode of rendering of URLs
			if(!empty($this->useRawUrls) || !empty($params['useRawUrls'])) {
				// Set mode for the global router to RAW
				$router = JFactory::getApplication()->getRouter();
				if (!empty($router)) {
					$sefMode = $router->getMode();
					$router->setMode(JROUTER_MODE_RAW);
				}
			}

			// first pass of rendering.
			$this->parse();
			$this->_template->content = $this->_renderTemplate();

			// The second pass. Some dynamic data can contain placeholders.
			// In other words - placeholders in placeholders...
			$this->_parseTemplate();
			$this->_template->content = $this->_renderTemplate();

			$this->_parseTemplate();
			$this->_template->content = $this->_renderTemplate();

			// Set absolute links
			// TODO: Need to move it in mailer. Because this is a scope of letter creation not template.
			$this->repairLinks($this->_template->content);

			if (!empty($params['newsletter_id'])) {
				$this->dispatcher->trigger('onMigurAfterNewsletterRender', array(
					&$this->_template->content,
					array('newsletter_id' => $params['newsletter_id'])
				));
			}

			// Add tracking by com_newsletter
			// TODO: Need to move it in mailer. Because this is a scope of letter creation not template.
			if (!empty($this->tracking) && !empty($params['newsletter_id'])) {
				$this->track(
					$this->_template->content,
					NewsletterHelperPlaceholder::getPlaceholder('subscription key'),
					$params['newsletter_id']
				);
			}

			// Restore the mode of rendering of URLs
			if(!empty($router) && isset($sefMode)) {
				$router->setMode($sefMode);
			}

			return $this->_template->content;

		} catch(Exception $e) {

			return false;
		}
	}

	/**
	 * Render pre-parsed template
	 *
	 * @return string rendered template
	 * @since  1.0
	 */
	protected function _renderTemplate()
	{
		$replace = array();
		$with = array();

		if (empty($this->_parsed)) {
			$this->_parsed = array();
		}

		foreach ($this->_parsed as $parsedTag) {
			foreach ($parsedTag AS $jdoc => $args) {
				$replace[] = $jdoc;
				$with[] = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
			}
		}

		$res = str_replace($replace, $with, $this->_template->content);
		return $res;
	}

	/**
	 * Maps the names of elements (may be from template)
	 * to a proper names used in renders (may be the placeholder name).
	 *
	 * @param  array $params - the array of items
	 *
	 * @return array - the array of items
	 * @since  1.0
	 */
	protected function _mapParams($params)
	{

		$map = array(
			'width_column1' => 'width_column1',
			'height_column1' => 'height_column1',
			'width_column2' => 'width_column2',
			'height_column2' => 'height_column2',
			'width_column3' => 'width_column3',
			'height_column3' => 'height_column3',
			'image_top' => 'image_top.src',
			'image_top_alt' => 'image_top.alt',
			'image_top_width' => 'image_top.width',
			'image_top_height' => 'image_top.height',
			'image_bottom' => 'image_bottom.src',
			'image_bottom_alt' => 'image_bottom.alt',
			'image_bottom_width' => 'image_bottom.width',
			'image_bottom_height' => 'image_bottom.height',
			'table_background' => 'table_background',
			'text_color' => 'text_color',
			't_style_id' => 't_style_id'
		);

		$res = array();
		if (is_array($params)) {
			foreach ($params as $name => $val) {
				if (array_key_exists($name, $map)) {
					$res[$map[$name]] = $val;
				} else {
					if (array_search($name, $map)) {
						$res[$name] = $val;
					}
				}
			}
		}
		return $res;
	}

	/**
	 * Set the contents a document include
	 *
	 * @param  string	$content	The content to be set in the buffer.
	 * @param  array	$options	Array of optional elements.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options)) {
			$args = func_get_args();
			$options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
		}

		parent::$_buffer[$options['type']][$options['name']] = $content;
	}

	/**
	 * Get the contents of a document include
	 *
	 * @param  string $type	The type of renderer
	 * @param  string $name	The name of the element to render
	 * @param  array $attribs Associative array of remaining attributes.
	 *
	 * @return The output of the renderer
	 * @since  1.0
	 */
	public function getBuffer($type = null, $name = null, $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null) {
			return parent::$_buffer;
		}

		$result = null;
		if (isset(parent::$_buffer[$type][$name])) {
			return parent::$_buffer[$type][$name];
		}

		// If the buffer has been explicitly turned off don't display or attempt to render
		if ($result === false) {
			return null;
		}

		$renderer = $this->loadRenderer($type);
		if ($this->_caching == true && $type == 'modules') {
			$cache = JFactory::getCache('com_modules', '');
			$hash = md5(serialize(array($name, $attribs, $result, $renderer)));
			$cbuffer = $cache->get('cbuffer_' . $type);

			if (isset($cbuffer[$hash])) {
				return JCache::getWorkarounds($cbuffer[$hash], array('mergehead' => 1));
			} else {

				$options = array();
				$options['nopathway'] = 1;
				$options['nomodules'] = 1;
				$options['modulemode'] = 1;

				$this->setBuffer($renderer->render($name, $attribs, $result), $type, $name);
				$data = parent::$_buffer[$type][$name];

				$tmpdata = JCache::setWorkarounds($data, $options);


				$cbuffer[$hash] = $tmpdata;

				$cache->store($cbuffer, 'cbuffer_' . $type);
			}
		} else {
			$this->setBuffer($renderer->render($name, $attribs, $result), $type, $name);
		}

		return parent::$_buffer[$type][$name];
	}

	/**
	 * The default method. Need to owerride in children.
	 *
	 * @return object - the template
	 * @since  1.0
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Some links generated by modules are relative.
	 * This method repairs links and add the base to each href or src
	 * From 12.04 added ability to use RAW absolute links to make these
	 * independent from settings of J!(SEF on/off). #495 in redmine.
	 *
	 * @param string $content - the content of a letter
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function repairLinks(&$content)
	{
		$content = NewsletterHelperContent::pathsToAbsolute($content);
		return true;
	}

	public function getNewsletterId() {
		return isset($this->_letter->newsletter_id)? $this->_letter->newsletter_id : null;
	}

	public function getContent() {
		return $this->_template->content;
	}

	public function setContent($content) {
		$this->_template->content = (string)$content;
	}
}
