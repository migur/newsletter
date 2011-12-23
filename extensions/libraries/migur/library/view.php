<?php

/**
 * The extension for JView
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.application.component.view');

/**
 * Class to extent the JView functionality
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurView extends JView
{

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param string The name of the template source file ...
	 * automatically searches the template paths and compiles as needed.
	 * @param string The name of the layout...
	 * null or absent - use current layout (don't change behavior),
	 * '' - no layout prefix in the name of file
	 * not empty string - uses specified layout.
	 *
	 * @return string The output of the the template script.
	 * @since  1.0
	 */
	public function loadTemplate($tpl = null, $layout = null)
	{
		if (!is_string($layout)) {
			return parent::loadTemplate($tpl);
		}

		// clear prior output
		$this->_output = null;

		$template = JFactory::getApplication()->getTemplate();
		$layoutTemplate = $this->getLayoutTemplate();


		if ($layout == '') {
			if (empty($tpl)) {

				return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
			} else {

				$file = $tpl;
			}
		} else {
			//create the template file name based on the layout
			$file = isset($tpl) ? $layout . '_' . $tpl : $layout;
		}

		// clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, false)
			|| $lang->load('tpl_' . $template, JPATH_BASE, $lang->getDefault(), false, false)
			|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", $lang->getDefault(), false, false);

		// change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template) {
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}

		// load the template script
		jimport('joomla.filesystem.path');
		$filetofind = $this->_createFileName('template', array('name' => $file));
		$this->_template = JPath::find($this->_path['template'], $filetofind);

		// If alternate layout can't be found, fall back to default layout
		if ($this->_template == false) {
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		if ($this->_template != false) {
			// unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// never allow a 'this' property
			if (isset($this->this)) {
				unset($this->this);
			}

			// start capturing output into a buffer
			ob_start();
			// include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		} else {
			return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file));
		}
	}
	
	
	
	/**
	 * Automatic adding the flying message script for popups
	 */
	public function display($tpl = null)
	{
		if (JRequest::getCmd('tmpl') == 'component') {
			
			$doc = JFactory::getDocument();
			$doc->addScript(JUri::root() . 'media/com_newsletter/js/migur/js/message.js');
		}	
		
		return parent::display($tpl);
	}
}

