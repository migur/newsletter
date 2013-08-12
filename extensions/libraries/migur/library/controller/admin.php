<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	// TODO deprecated since 12.1 Use PHP Exception
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

/**
 * Extension for Joomla Administrator Controller
 *
 * @since   13.05
 * @package Migur.Newsletter
 * 
 */
class MigurControllerAdmin extends JControllerAdmin
{

	/**
	 * Override of the parent method to fix deletion bug.
	 *
	 * @return  void
	 * @since   13.05a
	 */
	public function delete()
	{
		// Main goal is to fix 3.1.1 J! bug in case if no items were selected:
		// "fatal error: Argument 1 passed to JControllerAdmin::postDeleteHook() must be an instance of JModelLegacy, null given."
		// $this->postDeleteHook($model, $cid); <- $model is defined only if there are some items selected
		
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
		}
		else
		{
			parent::delete();
		}
	}

}
