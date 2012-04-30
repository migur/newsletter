<?php

/**
 * The controller for subscriber view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerSubscriber extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('savenclose', 'save');
	}

	/**
	 * Assign the subscriber to the list
	 * 
	 * @return void
	 * @since 1.0
	 */
	public function assign()
	{
		if (JRequest::getMethod() == "POST") {

			try {
				
				$sid = JRequest::getInt('subscriber_id', null, 'post');
				$lid = JRequest::getInt('list_to_subscribe', null, 'post');
				
				$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
				
				if (!$model->load($sid)) {
					throw new Exception();
				}
				
				if (!$model->assignToList($lid)) {
					throw new Exception();
				} 

				$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_SUCCESS"));
				
			} catch (Exception $e) {
				
				$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_FAILED"), 'error');
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($sid, 'subscriber_id'), false));
	}

	/**
	 * Unbind the subscriber from the list
	 *
	 * @return void
	 * @since 1.0
	 */
	public function unbind()
	{
		if (JRequest::getMethod() == "POST") {

			try {
				
				$sid = JRequest::getInt('subscriber_id', null, 'post');
				$lid = JRequest::getInt('list_to_unbind', null, 'post');
				
				$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
				
				if (!$model->load($sid)) {
					throw new Exception();
				}
				
				if (!$model->unbindFromList($lid)) {
					throw new Exception();
				} 

				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
				
			} catch (Exception $e) {
				
				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($sid, 'subscriber_id'), false));
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	public function save()
	{
		if (parent::save()) {
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
					break;
			}
			return true;
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
	 * Load data to autocreate subscriber row for J! user
	 */
	public function edit($key = null, $urlVar = null)
	{
		$sid = JRequest::getInt('subscriber_id');
		if ($sid < 0) {
			$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
			$model->load($sid);
			JRequest::setVar('subscriber_id', $model->getId());
			unset($model);
		}	
		
		return parent::edit($key, $urlVar);
	}
}

