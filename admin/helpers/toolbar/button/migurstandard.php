<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

MigurToolbar::getInstance()->loadButtonType('standard');
/**
 * Renders a standard button
 *
 * @package     Migur.com_newsletter
 * @since       13.06
 */
class JToolbarButtonMigurstandard extends JToolbarButtonStandard
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Migurstandard';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type  Unused string.
	 * @param   string   $name  The name of the button icon class.
	 * @param   string   $text  Button text.
	 * @param   string   $task  Task associated with the button.
	 * @param   boolean  $list  True to allow lists
	 *
	 * @return  string  HTML string for the button
	 *
	 * @since   13.08
	 */
	public function fetchButton($type = 'Migurstandard', $name = '', $text = '', $task = '', $list = true)
	{
		$i18n_text = JText::_($text);
		$class = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($text, $task, $list);

		if ($name == "apply" || $name == "new")
		{
			$btnClass = "btn btn-small btn-success";
			$iconWhite = "icon-white";
		}
		else
		{
			$btnClass = "btn btn-small";
			$iconWhite = "";
		}

		$html = "<button onclick=\"$doTask; return false;\" class=\"" . $btnClass . "\">\n";
		$html .= "<i class=\"$class $iconWhite\">\n";
		$html .= "</i>\n";
		$html .= "$i18n_text\n";
		$html .= "</button>\n";

		return $html;
	}
}
