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
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */

require_once 'migurbasic.php';

class JToolbarButtonMigurmodal extends JToolbarButtonMigurbasic
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'MigurModal';

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
		$params = $this->_getParams($args);

		if (!empty($params['modal']) && !empty($params['url'])) {
			$url = JRoute::_($params['url'], false);
			$params['onclick'] = "event && (event.returnValue = false); Migur.modal.show('{$params['modal']}', {'href': '{$url}'}); return false;";
			unset($params['modal']);
			unset($params['url']);
		}

		return $this->_getHtml($params);
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
		return $this->_parent->getName().'-'."migurmodal";
	}

}
