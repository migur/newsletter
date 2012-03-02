<?php

/**
 * The controller for lsit view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerList extends JControllerForm
{

	/**
	 *
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('savenclose', 'save');
		
		$this->view_list = 'subscribers';
	}

	
	/**
	 * See parent's phpdoc
	 * 
	 * @return  boolean
	 * @since   11.1
	 */
	protected function allowAdd($data = array(), $key = 'id')
	{
		return 
			/* parent::allowAdd($data, $key) && */
			AclHelper::actionIsAllowed('list.add');
	}


	/**
	 * See parent's phpdoc
	 * 
	 * @return  boolean
	 * @since   11.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return 
			/* parent::allowEdit($data, $key) && */
			AclHelper::actionIsAllowed('list.edit');
	}

	
	/**
	 * Save the configuration
	 * @return	boolean
	 * @since	1.0
	 */
	function save()
	{
		if (parent::save()) {
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
					break;
			}

			return true;
		} else {
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $key), false));
		}

		return false;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl = JRequest::getCmd('tmpl', 'component');
		$layout = JRequest::getCmd('layout');
		$append = '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout) {
			$append .= '&layout=' . $layout;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Is used for standard upload of file.
	 * @since  1.0
	 * @return void
	 */
	public function upload()
	{

		$listId = JRequest::getInt('list_id', 0);
		$subtask = JRequest::getString('subtask', 'import');

		if ($listId > 0) {

			$uploader = JModel::getInstance('file', 'NewsletterModel');
			$data = $uploader->upload(array(
					'overwrite' => true,
					'filedataName' => 'Filedata-' . $subtask
				));

			if (!empty($data['file'])) {

				// get the column names from uploaded file
				$arr = file($data['file']['filepath']);

				$data['fields'] = explode(',', $arr[0]);
			}
		}

		if (empty($data)) {
			$data = array();
		}

		$sess = JFactory::getSession();
		$sess->set('list.' . $listId . '.file.uploaded', $data);

		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($listId, 'list_id') . '&subtask=' . $subtask, false));
		return;
	}

	/**
	 * Assign the subscriber to the list
	 * @since  1.0
	 * @return void
	 */
	public function assignGroup()
	{
		if (!$this->allowEdit($data, $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
			return false;
		}
		
		
		if (JRequest::getMethod() == "POST") {

			$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

			$subscribers = JRequest::getVar('cid', null, 'post');
			$lists = json_decode(JRequest::getVar('list_id', null, 'post'));

			if (!empty($lists) && !empty($subscribers)) {
				
				foreach ($subscribers as $subscriberId) {
					// Need to load to add row  for j! user "on the fly"
					$model->load($subscriberId);
					
					foreach($lists as $listId) {
						
						if ($model->assignToList($listId)) {
							$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_SUCCESS"));
						} else {
							$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_FAILED"), 'error');
							break(2);
						}
					}
				}
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 * Unbind the subscriber to the list
	 * @since  1.0
	 * @return void
	 */
	public function unbindGroup()
	{
		if (!$this->allowEdit($data, $key)) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));
			return false;
		}
		
		
		if (JRequest::getMethod() == "POST") {

			$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

			$subscribers = JRequest::getVar('cid', null, 'post');
			$lists = json_decode(JRequest::getVar('list_id', null, 'post'));

			if (!empty($lists) && !empty($subscribers)) {

				foreach ($subscribers as $subscriberId) {
					// Need to load to add row  for j! user "on the fly"
					$model->load($subscriberId);
					
					foreach($lists as $listId) {

						if ($model->unbindFromList($listId)) {
							$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
						} else {
							$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
							break(2);
						}
					}
				}
			}
		}
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view='.$this->view_list, false));
	}
}

