<?php
/**
 * The controller for lists view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NewsletterControllerListevents extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_LISTEVENTS';
	
	
	
	public function getItems()
	{
		NewsletterHelperNewsletter::jsonPrepare();
		
		$listId = JRequest::getInt('list_id');
		
		$model = MigurModel::getInstance('List', 'NewsletterModel');
		
		try {
			$status = true;
			$message = null;
			$collection = $model->getEventsCollection($listId);
			
		} catch(Exception $e) {
			$message = $e->getMessage();
			$status = false;
			$collection = array();
		}
		
		NewsletterHelperNewsletter::jsonResponse($status, $message, $collection);
	}

	
	
	public function delete()
	{
		NewsletterHelperNewsletter::jsonPrepare();

		parent::delete();
		
		$status = ($this->messageType == 'message');
		NewsletterHelperNewsletter::jsonResponse($status, $this->message);
	}
	
}