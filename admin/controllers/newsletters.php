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
		
		$unset = 0;
		
		if (!empty($cids)) {
			
			
			foreach($cids as $idx => $cid) {
				$newsletter = NewsletterHelper::get($cid);
				if ($newsletter['used_as_static'] == 1 || !$newsletter['saveable']) {
					unset($cids[$idx]);
					$unset++;
					
				}
			}
			JRequest::setVar('cid', $cids);
		}
		
		if ($unset > 0) {
			JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_NEWSLETTER_SOME_COULDNOT_DELETE'), $unset), 'message');
		}
		
		parent::delete();
	}
}