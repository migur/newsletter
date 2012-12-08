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

/**
 * Legacy support
 */
JToolbar::getInstance()->loadButtonType('help');

class JToolbarButtonMigurSendProgress extends JButton
{
	/**
	 * @var    string	Button type
	 */
	protected $_name = 'MigurSendProgress';

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
	public function fetchButton($type = 'MigurHelp', $ref = '', $com = false, $override = null, $component = null, $target = '_blank', $width='980', $height='600')
	{
		
		$stat = MigurModel::getInstance('Queues', 'NewsletterModel')->getSummary();
		$sent = 0;
		$toSend = 0;
		$total = 0;
		foreach ($stat as $row) {
			$sent += $row['sent'];
			$total += $row['total'];
		}
		
		$text	= !empty($com)? JText::_($com) : JText::_('JTOOLBAR_HELP');
		$class	= $this->fetchIconClass('help');

		$html = 
			'<div style="float:left;font-size:12px;">' .
				'<div class="progress-info">' .
	                $sent . ' of ' . $total . ' mails sent in ' . count($stat) . ' newsletters' .
		        '</div>' .
				'<div style="float:right; min-width:0;" id="process-preloader"></div>' .
			'</div>' .
			'<div style="float:right">' .
			'</div>' .
			'<div style="width: 260px">' .
				'<div class="progress-line"></div>' .
				'<div class="progress-bar"></div>' .
			'</div>';	
		
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
		return $this->_parent->getName().'-'."migurhelp";
	}
}
