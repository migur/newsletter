<?php

/**
 * The plain document type main class
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.application.module.helper');
jimport('joomla.language.language');
jimport('joomla.utilities.simplexml');
JLoader::register('MigurDocumentRenderer', dirname(__FILE__) . DS . 'renderer.php');
JLoader::register('JDocumentHTML', JPATH_LIBRARIES . DS . 'joomla' . DS . 'document' . DS . 'html' . DS . 'html.php');


jimport('joomla.document.document');

/**
 * DocumentPlain class, provides an easy interface to parse and display an html document
 * 
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurMailerDocumentPlain extends MigurMailerDocument
{

	public $directory;

	/**
	 * Parses the template and populates the buffer
	 *
	 * @param  array parameters for fetching the template
	 * @return void
	 * @since  1.0
	 */
	public function parse($params = array())
	{

		$this->parsedTags = array();
		$this->parsedTags["placeholders"] = array(
			'regexp' => '#\[([\w\s\.]+)\]#iU',
			'matches' => array(
				'name' => '',
				'type' => 'placeholder',
				'attribs' => ''
			)
		);

		$this->_parseTemplate();
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
	protected function _loadTemplate($params)
	{
		$template = new stdClass();
		$template->content = $this->_letter->plain;
		return $template;
	}

	/**
	 * Load the renderer
	 *
	 * @param	string	The renderer type
	 * 
	 * @return	object
	 * @since   1.0
	 */
	public function loadRenderer($type)
	{
		$class = 'MigurDocumentPlainRenderer' . $type;

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
	 * The tracking is not implemented for PLAIN type
	 *
	 * @return	object
	 * @since   1.0
	 */
	public function track() {
		return true;
	}
	
	
	/**
	 * The plugins are not used for PLAIN type
	 *
	 * @return	object
	 * @since   1.0
	 */
	public function triggerEvent() {
		return true;
	}
}
