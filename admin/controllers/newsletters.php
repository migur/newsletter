<?php

/**
 * The controller for newsletters view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NewsletterControllerNewsletters extends JControllerAdmin
{

	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_NEWSLETTERS';

	/**
	 * Proxy for getModel.
	 *
	 * @return  object - the model object
	 * @since	1.0
	 */
	public function getModel($name = 'Newsletter', $prefix = 'NewsletterModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	
	public function delete() {
		
		$cids = JRequest::getVar('cid', array());
		
		$unsets = array();
		
		if (!empty($cids)) {
			
			
			foreach($cids as $idx => $cid) {
				$newsletter = NewsletterHelper::get($cid);
				if ($newsletter['used_as_static'] == 1 || !$newsletter['saveable']) {
					$unsets[] = $cids[$idx];
					unset($cids[$idx]);
				}
			}
			JRequest::setVar('cid', $cids);
		}
		
		if (count($unsets) > 0) {
			
			$deleteLink = '<a class="micro-button" href="'.JRoute::_('index.php?option=com_newsletter&task=newsletters.deletehard&'.JSession::getFormToken().'=1&cid='.implode(',', $unsets)).'">&nbsp;X&nbsp;</a>';
			
			JFactory::getApplication()->enqueueMessage(sprintf(
				JText::_('COM_NEWSLETTER_SOME_COULDNOT_DELETE'), 
				count($unsets),
				$deleteLink), 'message');
		}
		
		if (count($cids) > 0) {
			parent::delete();
		}	
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	
	public function deletehard() {
		
		$cid = JRequest::getString('cid', '');
		$cid = explode(',', $cid);
		
		if (count($cid)) {
			
			JRequest::setVar('cid', $cid, 'post');
			
			$token = JRequest::getVar(JSession::getFormToken());
			JRequest::setVar(JSession::getFormToken(), $token, 'post');
			
			parent::delete();
		}	
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
}