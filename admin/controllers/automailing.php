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

class NewsletterControllerAutomailing extends JControllerForm
{
	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function save()
	{
		$aid = JRequest::getInt('automailing_id');
		
		$isNew = empty($aid);
		JRequest::setVar('layout', (($isNew)? 'default' : 'edit'));

		
		if (parent::save()) {
			
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=close&tmpl=component', false));
					break;
			}

			return true;
			
		} else {
			
			$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=' . $this->view_item . $this->getRedirectToItemAppend($aid, 'automailing_id'), false));
		}

		return false;
	}
	
	
	/**
	 * Assign the subscriber to the list
	 * 
	 * @return void
	 * @since 1.0
	 */
	public function assignList()
	{
		$aid = JRequest::getInt('automailing_id', null, 'post');
		
		if (JRequest::getMethod() == "POST") {

			$table = JTable::getInstance('AutomailingTarget', 'NewsletterTable');
			$res = $table->save(array(
				'automailing_id' => $aid,
				'target_id' => JRequest::getInt('list_to_subscribe', null, 'post'),
				'target_type' => 'list'
			));
			
			if ($res) {
				$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_SUCCESS"));
			} else {
				$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_FAILED"), 'error');
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=automailing'.$this->getRedirectToItemAppend($aid, 'automailing_id'), false));
	}

	
	/**
	 * Assign the subscriber to the list
	 * 
	 * @return void
	 * @since 1.0
	 */
	public function unbindList()
	{
		$aid = JRequest::getInt('automailing_id', null, 'post');
		
		if (JRequest::getMethod() == "POST") {

			$lid = JRequest::getInt('list_to_unbind', null, 'post');
			
			$table = JTable::getInstance('AutomailingTarget', 'NewsletterTable');
			$res = $table->load(array(
				'automailing_id' => $aid,
				'target_id' => JRequest::getInt('list_to_unbind', null, 'post'),
				'target_type' => 'list'
			));
			$table->delete();
			
			
			if ($res) {
				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
			} else {
				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=automailing'.$this->getRedirectToItemAppend($aid, 'automailing_id'), false));
	}

	
	/**
	 * Unbind the subscriber from the list
	 *
	 * @return void
	 * @since 1.0
	 */
	public function unbindItem()
	{
		$aid = JRequest::getInt('automailing_id', null, 'post');
		$iid = JRequest::getInt('item_id', null, 'post');
		
		if (JRequest::getMethod() == "POST") {

			$table = JTable::getInstance('AutomailingItem', 'NewsletterTable');
			$res = $table->delete($iid);

			if ($res) {
				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
			} else {
				$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=automailing'.$this->getRedirectToItemAppend($aid, 'automailing_id'), false));
	}
}

