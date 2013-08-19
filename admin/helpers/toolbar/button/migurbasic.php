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
 * Usage:
 *		$bar->appendButton(
 *			'Migurbasic',
 *			'COM_NEWSLETTER_SHOW_STATISTICS',
 *			array('id' => 'ctrl-showstats', 'url' => JRoute::_('index.php?option=com_newsletter&view=statistic'))
 *		);
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */

class JToolbarButtonMigurbasic extends JToolbarButton
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'MigurBasic';
	protected $_defaults = array('class' => 'btn btn-small');

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
		return $this->_getHtml(
			$this->_getParams($args)
		);
	}

	protected function _getParams($args)
	{
		$this->_text = !empty($args[1])? $args[1] : '';
		$params = !empty($args[2])? $args[2] : array();

		return array_merge($this->_defaults, $params);
	}

	protected function _getHtml($params)
	{
		$strBtnProps = '';
		$strIconProps = '';

		if (empty($params['onclick'])) {
			$params['onclick'] = "return false;";
		}

		foreach ($params as $name => $val) {
			if (strpos($name, 'icon-') !== false) {
				$strIconProps .= str_replace('icon-', '', $name) . '="' . $val . '" ';
			} else {
				$strBtnProps .= $name . '="' . $val . '" ';
			}
		}

		$html = "<button $strBtnProps>\n";
		$html .= "<span $strIconProps>\n";
		$html .= "</span>\n";
		$html .= JText::_($this->_text) . "\n";
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
		return $this->_parent->getName().'-'."migurbasic";
	}
}
