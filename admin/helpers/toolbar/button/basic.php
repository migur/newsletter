<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

/**
 * Renders a help popup window button
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */

class JToolbarButtonBasic extends JToolbarButton
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'Basic';

	/**
	 * @param   string   $type		Unused string.
	 * @param   string   $ref		The name of the help screen (its key reference).
	 * @param   boolean  $com		Use the help file in the component directory.
	 * @param   string   $override	Use this URL instead of any other.
	 * @param   string   $component	Name of component to get Help (null for current component)
	 *
	 * @return  string
	 * @since   11.1
	 */
	public function fetchButton()
	{
		$args = func_get_args();
		$text = !empty($args[1])? $args[1] : '';
		$btnProps = !empty($args[2])? $args[2] : array();
		$spanProps = !empty($args[3])? $args[3] : array();
		
		$btnProps = array_merge(array('class' => 'btn btn-small'), $btnProps);
		
		$strBtnProps = '';
		foreach ($btnProps as $name => $val) {
			$strBtnProps .= $name . '="' . $val . '" ';
		}

		$strSpanProps = '';
		foreach ($spanProps as $name => $val) {
			$strSpanProps .= $name . '="' . $val . '" ';
		}

		$html = "<button $strBtnProps>\n";
		$html .= "<span $strSpanProps>\n";
		$html .= "</span>\n";
		$html .= JText::_($text) . "\n";
		$html .= "</button>\n";

		return $html;
	}
	
	/**
	 * Get the button id
	 *
	 * Redefined from JButton class
	 *
	 * @return  string	Button CSS Id
	 * @since       11.1
	 */
	public function fetchId()
	{
		return $this->_parent->getName().'-'."help";
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $ref		The name of the help screen (its key reference).
	 * @param   boolean  $com		Use the help file in the component directory.
	 * @param   string   $override	Use this URL instead of any other.
	 * @param   string   $component	Name of component to get Help (null for current component)
	 *
	 * @return  string   JavaScript command string
	 * @since   11.1
	 */
	protected function _getCommand($ref, $com, $override, $component, $width, $height)
	{
		// Get Help URL
		jimport('joomla.language.help');
		$url = JHelp::createURL($ref, $com, $override, $component);
		$url = htmlspecialchars($url, ENT_QUOTES);
		$cmd = "Joomla.popupWindow('$url', '".JText::_('JHELP', true)."', $width, $height, 1)";

		return $cmd;
	}
}
