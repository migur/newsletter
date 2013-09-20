<?php

/**
 * The controller for automailing view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerListevent extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Preserve list_id as the eventlist is a ALWAYS child of a some LIST.
		$lid = JRequest::getInt('list_id', null);
		if (!empty($lid)) {
			$jform = JRequest::getVar('jform');
			$jform['list_id'] = $lid;
			JFactory::getApplication()->input->set('jform', $jform);
			JRequest::setVar('jform', $jform);
		}
	}


	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function save()
	{
		if (parent::save()) {
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=close&tmpl=component', false));
					return;
			}
		}
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function delete()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = (array) JRequest::getInt('le_id', null);

		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid)) {
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			} else {
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');

		$url = JRequest::getString('returnUrl');

		$url = !empty($url) ?
			base64_decode(urldecode($url)) :
			JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false);

		$this->setRedirect($url);
	}

	public function cancelpopup()
	{
		$this->view_list = 'close';
		parent::cancel();
	}

	/**
	 * In addition adds parent id (list_id)
	 *
	 * @param type $recordId
	 * @param type $urlVar
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'le_id')
	{
		$part = parent::getRedirectToItemAppend($recordId, $urlVar);
		$lid = JRequest::getInt('list_id', null);

		if (!empty($lid)) {
			$part .= '&list_id='. (int) $lid;
		}

		return $part;
	}

}

