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
 *		$bar->appendButton('Migurpreloader');
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */

require_once 'migurbasic.php';

class JToolbarButtonMigurpreloader extends JToolbarButtonMigurbasic
{
	protected $_defaults = array('id' => 'toolbar-preloader');

	/**
	 * @var    string	Button type
	 */
	protected $_name = 'MigurPreloader';



	protected function _getHtml($params)
	{
		$html = "<div id=\"{$params['id']}\" class=\"preloader-container\"></div>\n";
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
		return $this->_parent->getName().'-'."migurpreloader";
	}
}
