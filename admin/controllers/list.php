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
	 * Is used for standard upload of file.
	 * @since  1.0
	 * @return void
	 */
	public function upload()
	{
		$listId = JRequest::getInt('list_id', null);
		$callback = JRequest::getString('callback', '');
		
		if (empty($listId)) {
			throw new Exception (JText::_('COM_NEWSLETTER_LIST_ID_ABSENT'));
		}

		$app = JFactory::getApplication();
		
		$uploader = MigurModel::getInstance('file', 'NewsletterModel');
		$data = $uploader->upload(array(
			'overwrite' => true,
			'filedataName' => 'Filedata'
		));

		$msg = $data['error'];

		$sess = JFactory::getSession();
		
		if ($data['status'] == 1 && !empty($data['file'])) {

			$data = array(
				'status'   => $data['status'],
				'error'    => $data['error'],
				'file' => array(
					'name'     => $data['file']['name'],
					'type'     => $data['file']['type'],
					'tmp_name' => $data['file']['tmp_name'],
					'size'     => $data['file']['size'],
					'error'    => $data['file']['error'],
					'filepath' => $data['file']['filepath']
				)	
			);

			// These data are for further manipulations with file
			$sess->set('com_newsletter.list.'.$listId.'.file.uploaded', $data);
		}	

		// These data will be passed into JS importer onUpload
		$sess->set('com_newsletter.uploader.file', $data);
		
		$this->setRedirect(JRoute::_('index.php?'.implode('&', array(
			'option=com_newsletter',
			'view=uploader',
			'tmpl=component',
			'params[task]=list.upload',
			'params[callback]='.$callback,
			'params[list_id]='.$listId,
			'message='.$msg
		)), false));
		
		return;
	}

	/**
	 * Assign the subscriber to the list
	 * @since  1.0
	 * @return void
	 */
	public function assignGroup()
	{
		if (!$this->allowEdit()) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
			return false;
		}
		
		
		if (JRequest::getMethod() == "POST") {

			$subscriber  = MigurModel::getInstance('Subscriber', 'NewsletterModelEntity');
			$newsletter  = MigurModel::getInstance('Newsletter', 'NewsletterModelEntity');
			$listManager = MigurModel::getInstance('List', 'NewsletterModel');
			
			$subscribers = JRequest::getVar('cid', array(), 'post');
			$lists = json_decode(JRequest::getVar('list_id', array(), 'post'));

			if (!empty($lists) && !empty($subscribers)) {
				
				foreach ($subscribers as $subscriberId) {
					
					// Need to load to add row  for j! user "on the fly"
					$subscriber->load($subscriberId);

					$assignedLists = array();
					
					$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_SUCCESS"));

					foreach($lists as $listId) {
						
						if(!$subscriber->isInList($listId)) {
							
							try {

								// No need to send the subscription newsleter when assigning with admin
								
//								if (!$listManager->sendSubscriptionMail(
//									$subscriber,
//									$listId, 
//									array(
//										'addToQueue'       => true,
//										'ignoreDuplicates' => true))
//								){	
//									throw new Exception();
//								}
//								
								if(!$subscriber->assignToList($listId, array('confirmed' => true))){
									throw new Exception();
								}	

								$assignedLists[] = $listId;
								
							} catch (Exception $e) {
								
								$this->setMessage(JText::_("COM_NEWSLETTER_ASSIGN_FAILED"), 'error');

								LogHelper::addError(
									'COM_NEWSLETTER_WELCOMING_SEND_FAILED', LogHelper::CAT_SUBSCRIPTION, array(
									'Error' => $e->getMessage(),
									'Email' => $subscriber->email,
									'Newsletter' => $newsletter->name));
								
								return false;
							}	
						}	
					}
					
					// Fire event onMigurAfterSubscriberAssign
					JFactory::getApplication()->triggerEvent('onMigurAfterSubscriberAssign', array(
						'subscriberId' => $subscriber->getId(),
						'lists' => $assignedLists
					));
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
		if (!$this->allowEdit()) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));
			return false;
		}
		
		
		if (JRequest::getMethod() == "POST") {

			$model = MigurModel::getInstance('Subscriber', 'NewsletterModelEntity');

			$subscribers = JRequest::getVar('cid', null, 'post');
			$lists = json_decode(JRequest::getVar('list_id', null, 'post'));

			if (!empty($lists) && !empty($subscribers)) {

				foreach ($subscribers as $subscriberId) {
					// Need to load to add row  for j! user "on the fly"
					$model->load($subscriberId);

					$unboundLists = array();
					
					foreach($lists as $listId) {

						if($model->isInList($listId)) {
							if ($model->unbindFromList($listId)) {

								$unboundLists[] = $listId;
								$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_SUCCESS"));
							} else {
								$this->setMessage(JText::_("COM_NEWSLETTER_UNBIND_FAILED"), 'error');
								break(2);
							}
						}	
					}
					
					// Fire event onMigurAfterSubscriberUnbind
					JFactory::getApplication()->triggerEvent('onMigurAfterSubscriberUnbind', array(
						'subscriberId' => $model->getId(),
						'lists' => $unboundLists
					));
				}
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view='.$this->view_list, false));
	}
    
    
	/**
	 * Retrieves only first row(names of columns) of the list
	 * @since 1.0
	 * @return void
	 */
	public function gethead()
	{
		NewsletterHelper::jsonPrepare();

		$data = (array) json_decode(JRequest::getVar('jsondata', '{}'));
		
		if (!$settings = $this->_getSettings()) {
			return;
		}

		if (!empty($data['file'])) {
			if (($handle = fopen($data['file'], "r")) !== FALSE) {
				$data['fields'] = fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure);
			} else {
				NewsletterHelper::jsonError('Cannot open file', $data);
			}
		} else {
			NewsletterHelper::jsonError('No data about file', $data);
		}

		NewsletterHelper::jsonMessage('', $data);
	}


	
	/**
	 * Fetches and adds the data to DB from the file uploaded before
	 * @return void
	 * @since 1.0
	 */
	public function import()
	{
		NewsletterHelper::jsonPrepare();
		
		$app = JFactory::getApplication();
		
		$currentList = JRequest::getInt('list_id', '0');
	
		$limit = JRequest::getInt('limit', 1000);
		$offset = JRequest::getVar('offset', '');
		
		if ($currentList < 1) {
            NewsletterHelper::jsonError('No list Id');
		}

		if (!$settings = $this->_getSettings()) {
            NewsletterHelper::jsonError('No settings');
		}

		$mapping = $settings->fields;

		$sess = JFactory::getSession();
		
		sl_vd($sess->get('registry'));
		
		$file = $sess->get('com_newsletter.list.' . $currentList . '.file.uploaded', array());

		
		$filepath = $file['file']['filepath'];
		
		$statePath = 'com_newsletter.list-'.$currentList.'.import.'.md5($filepath);

		//$app->setUserState($statePath.'.trololo101', 'facepalm');

		// If there is no extarnal offset then use internal from session
		if (!is_numeric($offset)) {
			$offset = $app->getUserState($statePath.'.offset', 0);
		}	

		// If this is a start then init session variables
		if ($offset == 0) {
			$app->setUserState($statePath.'.seek', 0);
			$app->setUserState($statePath.'.skipped', 0);
			$app->setUserState($statePath.'.errors', 0);
			$app->setUserState($statePath.'.added', 0);
			$app->setUserState($statePath.'.updated', 0);
			$app->setUserState($statePath.'.assigned', 0);
		}
		
		// Restore previous state
		$seek     = $app->getUserState($statePath.'.seek', 0);
		$skipped  = $app->getUserState($statePath.'.skipped', 0);
		$errors   = $app->getUserState($statePath.'.errors', 0);
		$added	  = $app->getUserState($statePath.'.added', 0);
		$updated  =	$app->getUserState($statePath.'.updated', 0);
		$assigned =	$app->getUserState($statePath.'.assigned', 0);

		
		// Try to open file
		if (($handle = fopen($filepath, "r")) === FALSE) {
			NewsletterHelper::jsonError('Cannot open file');
		}

		//get the header and seek to previous position
		if ($settings->skipHeader) {
			fgetcsv($handle, 0, $settings->delimiter, $settings->enclosure);
		}	
		
		// Seek only if SEEK is not on the start to prevent inserting the HEADER into DB
		if ($seek > 0) {
			fseek($handle, $seek);
		}	
		
		$collection = array();
		$total = 0;

		while (
			($limit == 0 || $total < $limit) &&
			($data = fgetcsv($handle, 0, $settings->delimiter, $settings->enclosure)) !== FALSE
		) {
			if ($mapping->html->mapped === null || !isset($data[$mapping->html->mapped])) {
				$htmlVal = $mapping->html->default;
			} else {
				$htmlVal = $data[$mapping->html->mapped];
			}

			if (!empty($data[$mapping->username->mapped]) && !empty($data[$mapping->email->mapped])) {
				$collection[] = (object)array(
					'name' => $data[$mapping->username->mapped],
					'email' => $data[$mapping->email->mapped],
					'html' => $htmlVal
				);
			} else {
				$skipped++;
			}

			$total++;
		}

		// Store seek for further requests and close file
		$app->setUserState($statePath.'.seek', ftell($handle));

		
		
		// Let's import it all!
		$list = MigurModel::getInstance('List', 'NewsletterModel');
		$res = $list->importCollection(
			$currentList,
			$collection, 
			array(
				'overwrite' => $settings->overwrite,
				'autoconfirm' => true,
				'sendRegmail' => false
			));
		
		if (!empty($res['errors'])) {
			NewsletterHelper::jsonError('Import failed!', array(
				'fetched'   => $total,
				'total'     => $offset   + $total,
				'skipped'   => $skipped,
				'errors'    => $errors   + $res['errors'],
				'added'     => $added    + $res['added'],
				'updated'   => $updated  + $res['updated'],
				'assigned'  => $assigned + $res['assigned']));
		}

		// Check if this is not the end
		if ($total > 0) {
			
			// Store offsets and stats
			$app->setUserState($statePath.'.offset', $offset + $total);
			$app->setUserState($statePath.'.skipped', $skipped);
			$app->setUserState($statePath.'.errors', $errors + $res['errors']);
			$app->setUserState($statePath.'.added', $added + $res['added']);
			$app->setUserState($statePath.'.updated', $updated + $res['updated']);
			$app->setUserState($statePath.'.assigned', $assigned + $res['assigned']);

			NewsletterHelper::jsonMessage('ok', array(
				'fetched'   => $total,
				'total'     => $offset   + $total,
				'skipped'   => $skipped,
				'errors'    => $errors   + $res['errors'],
				'added'     => $added    + $res['added'],
				'updated'   => $updated  + $res['updated'],
				'assigned'  => $assigned + $res['assigned']));
		}

		// This is the end
		$app->setUserState($statePath.'.seek', 0);
		$app->setUserState($statePath.'.offset', 0);
		$app->setUserState($statePath.'.skipped', 0);
		$app->setUserState($statePath.'.errors', 0);
		$app->setUserState($statePath.'.added', 0);
		$app->setUserState($statePath.'.updated', 0);
		$app->setUserState($statePath.'.assigned', 0);

		//unlink($file['file']['filepath']);
		//$sess->clear('com_newsletter.list.' . $currentList . '.file.uploaded');

		NewsletterHelper::jsonMessage(JText::_('COM_NEWSLETTER_IMPORT_SUCCESSFUL'), array(
			'fetched'   => $total,
			'total'     => $offset + $total,
			'skipped'   => $skipped,
			'total'     => $offset   + $total,
			'errors'    => $errors   + $res['errors'],
			'added'     => $added    + $res['added'],
			'updated'   => $updated  + $res['updated'],
			'assigned'  => $assigned + $res['assigned']));
	}

	
	/**
	 * Unbind the users from list. The users are in the file uploaded before
	 * @return void
	 * @since 1.0
	 */
	public function exclude()
	{

		$subtask = JRequest::getString('subtask', '');
		$currentList = JRequest::getInt('list_id', 0);

		if ($currentList < 1) {
			echo json_encode(array('status' => '0', 'error' => 'No list Id'));
			return false;
		}

		if ($subtask == 'lists') {

			$data = json_decode(JRequest::getString('jsondata', ''));

			$list = MigurModel::getInstance('list', 'newsletterModel');

			$subscribers = array();

			foreach ($data->lists as $listId) {
				$res = array();
				$subs = $list->getSubscribers($listId);
				foreach ($subs as $item) {
					if (!in_array($item->subscriber_id, $subscribers))
						$subscribers[] = $item->subscriber_id;
				}
			}

			$mList = MigurModel::getInstance('List', 'NewsletterModel');
			$total = count($subscribers);
			
			foreach ($subscribers as $item) {
				$res = $mList->unbindSubscriber($currentList, $item);

				if (!$res) {

					NewsletterHelper::jsonError('COM_NEWSLETTER_EXCLUSION_FAILED', array(
						'total' => $total
					));
					return;
				}
			}

			NewsletterHelper::jsonMessage(JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'), array(
				'total' => $total
			));
			return;
		}

		if ($subtask == 'parse') {

			if (!$settings = $this->_getSettings()) {
				return;
			}
			
			$mapping = $settings->fields;

			$sess = JFactory::getSession();
			$file = $sess->get('com_newsletter.list.' . $currentList . '.file.uploaded', array());


			
			if (($handle = fopen($file['file']['filepath'], "r")) !== FALSE) {

				//get the header
				if ($settings->skipHeader) {
					fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure);
				}	

				while (($data = fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure)) !== FALSE) {
					$res[] = array(
						'email' => $data[$mapping->email->mapped],
					);
				}
				fclose($handle);

				$subscriber = MigurModel::getInstance('subscriber', 'newsletterModel');
				$mList = MigurModel::getInstance('List', 'NewsletterModel');

				$total = count($res);
				$absent = 0;
				$processed = 0;
				foreach ($res as $row) {

					$user = $subscriber->getItem(array('email' => $row['email']));

					if ($user) {
						$res = $mList->unbindSubscriber($currentList, $user['subscriber_id']);

						if (!$res) {
							NewsletterHelper::jsonError('COM_NEWSLETTER_EXCLUSION_FAILED', array(
								'processed' => $processed,
								'absent' => $absent,
								'total' => $total
							));
						} else {
							$processed++;
						}
					} else {
						$absent++;
					}
				}

				unlink($file['file']['filepath']);
				$sess->clear('com_newsletter.list.' . $currentList . '.file.uploaded');

				NewsletterHelper::jsonMessage(JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'), array(
					'processed' => $processed,
					'absent' => $absent,
					'total' => $total
				));
			}
		}
	}

	
	
	/**
	 * Retrieves the settings from the request data
	 * @return object - data
	 * @since 1.0
	 */
	protected function _getSettings()
	{

		$json = json_decode(JRequest::getString('jsondata', ''));

		if (empty($json->enclosure) || $json->enclosure == 'no') {
			$json->enclosure = "\0";
		}


		if (empty($json->delimiter)) {
			echo json_encode(array('status' => '0', 'error' => 'Some settings are absent'));
			return false;
		}

		switch ($json->delimiter) {
			case 'tab':
				$json->delimiter = "\t";
				break;
			case 'space':
				$json->delimiter = " ";
				break;
		}

		return $json;
	}
}

