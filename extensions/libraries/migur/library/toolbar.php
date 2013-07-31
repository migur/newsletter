<?php

/**
 * The implementation of the multiform toolbar
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

//TODO: It must be removed. The multiform functionality should be implemented
// with submitbutton.js

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

/**
 * Class that implements the multiform toolbar
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurToolBar extends JToolBar
{
	protected $_formName = '';
	
	protected $_actionPrefix = '';
	
	protected $_useAcl = false;
	
	protected $_options = array();

	/**
	 * The constructor of a class
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($name = 'toolbar', $form = null, $actionPrefix = '', $useAcl = false, $options = array())
	{
		parent::__construct($name);

		$this->_formName = ($form) ? $form : $name . 'Form';

        if (defined(COM_NEWSLETTER_PATH_ADMIN)) {
            $this->addButtonPath(COM_NEWSLETTER_PATH_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'toolbar' . DIRECTORY_SEPARATOR . 'button');
        }
        $this->addButtonPath(JPATH_LIBRARIES. DIRECTORY_SEPARATOR .'migur'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'button');

		$this->_actionPrefix = $actionPrefix;
		
		$this->_useAcl = $useAcl;
		
		$this->_options = (array) $options;
	}

	/**
	 * Returns the global JToolBar object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param	string		$name  The name of the toolbar.
	 * 
	 * @return	JToolBar	The MigurToolBar object.
	 * @since   1.0
	 */
    public static function getInstance($name = 'toolbar', $form = null, $actionPrefix = '', $useAcl = false, $options = array())
    {
        if (!isset(self::$instances)) {
            self::$instances = array();
        }
        if (empty(self::$instances[$name])) {
            self::$instances[$name] = new MigurToolBar($name, $form, $actionPrefix, $useAcl, $options);
        }

        return self::$instances[$name];
    }

	/**
	 * Changes standard behavior.
	 *
	 * @param	object	A param tag node.
	 * @param	string	The control name.
	 *
	 * @return	array	Any array of the label, the form element and the tooltip.
	 * @since   1.0
	 */
	public function renderButton(&$node)
	{
		$html = parent::renderButton($node);
		$formName = $this->_formName;

		if (!empty($this->_options['preserveJCallback'])) {
			return $html;
		}	
		
		if (!empty($this->_options['useDefaultCallback'])) {
			return preg_replace(
				array(
					"/Joomla\.submitbutton\(([^)]*)\)/",
					"/adminForm/"
				),
				array(
					"Joomla.submitbutton($1, document.{$formName}, this)",
					$formName
				),
				$html
			);
		}
		
		return preg_replace(
			array(
				"/Joomla\.submitbutton\(([^)]*)\)/",
				"/adminForm/"
			),
			array(
				"Joomla.submitform($1, document.{$formName}, this)",
				$formName
			),
			$html
		);
	}
	
	public function appendButton() 
	{
		$args = func_get_args();

        $action = !empty($args[1])? $args[1] : '';
		
		if ($this->_useAcl) {
			if (!NewsletterHelperAcl::actionIsAllowed($this->_actionPrefix.'.'.$action)) {
				return false;
			}
		}
		
		return call_user_func_array(array('parent', 'appendButton'), $args);
	}
}
