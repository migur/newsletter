<?php
/**
 * The controller for queues view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NewsletterControllerQueues extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_QUEUES';

	/**
	 * Proxy for getModel.
	 *
	 * @since	1.0
	 */
	public function getModel($name = 'Queue', $prefix = 'NewsletterModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	
	
	/**
	 * Check each element and delete deleteable ones
	 */
	public function delete() {
		
		$cids = JRequest::getVar('cid', array());
		
		$unset = 0;
		
		if (!empty($cids)) {

			$model = JModel::getInstance('queue', 'NewsletterModelEntity');
			
			foreach($cids as $idx => $cid) {
				
				$model->load($cid);
				
				if ($model->isSent()) {
					unset($cids[$idx]);
					$unset++;
				}
			}

			if ($unset > 0) {
				JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_NEWSLETTER_QUEUES_ITEMS_CANNOT_DELETED'), $unset), 'message');
			}
			
			if (empty($cids)) {
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
				return;
			}
			
			JRequest::setVar('cid', $cids);
		}
		
		
		parent::delete();
	}
}