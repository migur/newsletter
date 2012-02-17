<?php
/**
 * The controller for conflicts view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NewsletterControllerConflicts extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_CONFLICTS';

	/**
	 * Proxy for getModel.
	 *
	 * @since	1.0
	 */
	public function getModel($name = 'Conflict', $prefix = 'NewsletterModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	
	
	/**
	 * Deletes subscribers by cids
	 * 
	 * @since 12.02
	 */
	public function deleteSubs() 
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->deleteSubscribers($cid)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setError(JText::plural('COM_NEWSLETTER_CONFLICTS_MERGING_FAILED', count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}

	
	
	/**
	 * Merge all lists and history into users row
	 * 
	 * @since 12.02
	 */
	public function mergeUsers() 
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->mergePreservingUsers($cid)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_MERGED', count($cid)));
			} else {
				$this->setError(JText::plural('COM_NEWSLETTER_CONFLICTS_MERGING_FAILED', count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}

	
	
	/**
	 * Merge all lists and history into users row with replacing 
	 * user's migur data with subscriber's data.
	 * 
	 * @since 12.02
	 */
	public function mergeSubs() 
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->mergePreservingSubs($cid)) {
				$this->setMessage(JText::plural($this->text_prefix.'_N_ITEMS_MERGED', count($cid)));
			} else {
				$this->setError(JText::plural('COM_NEWSLETTER_CONFLICTS_MERGING_FAILED', count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
}