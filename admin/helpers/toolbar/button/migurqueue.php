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
 * Renders buttons for queue (used in dashboard)
 */
class JButtonMigurqueue extends JButton
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'MigurQueue';

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
	public function fetchButton($type = 'MigurQueue', $ref = '', $com = false, $override = null, $component = null, $target = '_blank', $width='980', $height='600')
	{

		$html =
			'<a style="clear:both" href="#" class="queue-list">' . JText::_('PROCESS_QUEUE') . '</a></br>' .
			'<a style="clear:both" href="index.php?option=com_newsletter&view=queues" class="viewqueue-list">' . JText::_('VIEW_QUEUE') . '</a><br/>' .
			'<a style="clear:both" href="#" class="bounces-list">' . JText::_('PROCESS_BOUNCES') . '</a>';

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
		return $this->_parent->getName().'-'."queue";
	}

}
