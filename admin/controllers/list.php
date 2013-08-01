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
			NewsletterHelperAcl::actionIsAllowed('list.add');
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
			NewsletterHelperAcl::actionIsAllowed('list.edit');
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
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend(), false));
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

			$uploader = MigurModel::getInstance('file', 'NewsletterModel');
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

								NewsletterHelperLog::addError(
									'COM_NEWSLETTER_WELCOMING_SEND_FAILED', NewsletterHelperLog::CAT_SUBSCRIPTION, array(
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
		NewsletterHelperNewsletter::jsonPrepare();

		$jsonData = JRequest::getString('jsondata', '{}');
		if (get_magic_quotes_gpc()) {
			$jsonData = stripslashes($jsonData);
		}
		
		$data = NewsletterHelperData::jsonDecode($jsonData);
		
		if (!$settings = $this->_getSettings($data)) {
			return;
		}

		if (!empty($data->file)) {
			if (($handle = fopen($data->file, "r")) !== FALSE) {
				$data->fields = fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure);
			} else {
				NewsletterHelperNewsletter::jsonError('Cannot open file', $data);
			}
		} else {
			NewsletterHelperNewsletter::jsonError('No data about file', $data);
		}

		NewsletterHelperNewsletter::jsonMessage('', $data);
	}


	
	/**
	 * Fetches and adds the data to DB from the file uploaded before
	 * @return void
	 * @since 1.0
	 */
	public function import()
	{
		NewsletterHelperNewsletter::jsonPrepare();
		
		$app = JFactory::getApplication();
		
		$currentList = JRequest::getInt('list_id', '0');
	
		$limit = JRequest::getInt('limit', 1000);
		$offset = JRequest::getVar('offset', '');
		
		if ($currentList < 1) {
            NewsletterHelperNewsletter::jsonError('No list Id');
		}

		$jsonData = JRequest::getString('jsondata', '{}');
		if (get_magic_quotes_gpc()) {
			$jsonData = stripslashes($jsonData);
		}
		
		$data = NewsletterHelperData::jsonDecode($jsonData);
		
		if (!$settings = $this->_getSettings($data)) {
            NewsletterHelperNewsletter::jsonError('No settings');
		}

		$mapping = $settings->fields;

		$sess = JFactory::getSession();
		$file = $sess->get('list.' . $currentList . '.file.uploaded', array());

		$filename = $file['file']['filepath'];
		$statePath = 'com_newsletter.'.md5('list.'.$currentList.'import.file.'.$filename);
		
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
			$app->setUserState($statePath.'.alreadyInList', 0);
		}
		
		// Restore previous state
		$seek     = $app->getUserState($statePath.'.seek', 0);
		$skipped  = $app->getUserState($statePath.'.skipped', 0);
		$errors   = $app->getUserState($statePath.'.errors', 0);
		$added	  = $app->getUserState($statePath.'.added', 0);
		$updated  =	$app->getUserState($statePath.'.updated', 0);
		$assigned =	$app->getUserState($statePath.'.assigned', 0);
		$alreadyInList = $app->getUserState($statePath.'.alreadyInList', 0);
		
		
		// Try to open file
		if (($handle = fopen($filename, "r")) === FALSE) {
			NewsletterHelperNewsletter::jsonError('Cannot open file');
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
		fclose($handle);

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
			NewsletterHelperNewsletter::jsonError('Import failed!', array(
				'fetched'   => $total,
				'total'     => $offset   + $total,
				'skipped'   => $skipped,
				'errors'    => $errors   + $res['errors'],
				'added'     => $added    + $res['added'],
				'updated'   => $updated  + $res['updated'],
				'assigned'  => $assigned + $res['assigned'],
				'alreadyInList' => $alreadyInList  + $res['alreadyInList']));
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
			$app->setUserState($statePath.'.alreadyInList', $alreadyInList + $res['alreadyInList']);

			NewsletterHelperNewsletter::jsonMessage('ok', array(
				'fetched'   => $total,
				'total'     => $offset   + $total,
				'skipped'   => $skipped,
				'errors'    => $errors   + $res['errors'],
				'added'     => $added    + $res['added'],
				'updated'   => $updated  + $res['updated'],
				'assigned'  => $assigned + $res['assigned'],
				'alreadyInList' => $alreadyInList + $res['alreadyInList']));
		}

		// This is the end
		$app->setUserState($statePath.'.seek', 0);
		$app->setUserState($statePath.'.offset', 0);
		$app->setUserState($statePath.'.skipped', 0);
		$app->setUserState($statePath.'.errors', 0);
		$app->setUserState($statePath.'.added', 0);
		$app->setUserState($statePath.'.updated', 0);
		$app->setUserState($statePath.'.assigned', 0);
		$app->setUserState($statePath.'.alreadyInList', 0);

		unlink($file['file']['filepath']);
		$sess->clear('list.' . $currentList . '.file.uploaded');

		$res = array(
			'fetched'   => $total,
			'total'     => $offset + $total,
			'skipped'   => $skipped,
			'total'     => $offset   + $total,
			'errors'    => $errors   + $res['errors'],
			'added'     => $added    + $res['added'],
			'updated'   => $updated  + $res['updated'],
			'assigned'  => $assigned + $res['assigned'],
			'alreadyInList' => $alreadyInList + $res['alreadyInList']);
		
		
		NewsletterHelperLog::addDebug(
			'Import successfull. List ID is '.$currentList, 
			NewsletterHelperLog::CAT_IMPORT, 
			$res
		);
		
		NewsletterHelperNewsletter::jsonMessage(JText::_('COM_NEWSLETTER_IMPORT_SUCCESSFUL'), $res);
	}

	
	/**
	 * Unbind the users from list. The users are in the file uploaded before
	 * @return void
	 * @since 1.0
	 */
	public function exclude()
	{
		NewsletterHelperNewsletter::jsonPrepare();
		
		$app = JFactory::getApplication();
		
		$subtask = JRequest::getString('subtask', '');
		$lids = (array) JRequest::getVar('list_id', 0);
        $currentList = (int) $lids[0];
        
		$limit = JRequest::getInt('limit', 1000);
		$offset = JRequest::getVar('offset', '');
		
		if ($currentList < 1) {
            NewsletterHelperNewsletter::jsonError('No list Id');
		}

		$jsonData = JRequest::getString('jsondata', '{}');
		if (get_magic_quotes_gpc()) {
			$jsonData = stripslashes($jsonData);
		}
		
		$data = NewsletterHelperData::jsonDecode($jsonData);
		
		if ($subtask == 'lists') {

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
			
			$dbo = JFactory::getDbo();
			$dbo->transactionStart();
			
			foreach ($subscribers as $item) {
				
				$res = $mList->unbindSubscriber($currentList, $item);
				
				if (!$res) {

					NewsletterHelperNewsletter::jsonError('COM_NEWSLETTER_EXCLUSION_FAILED', array(
						'total' => $total
					));
					return;
				}
			}

			$dbo->transactionCommit();
			
			NewsletterHelperNewsletter::jsonMessage(JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'), array(
				'total' => $total
			));
			return;
		}

		if ($subtask == 'parse') {
            
            if (!$settings = $this->_getSettings($data)) {
                NewsletterHelperNewsletter::jsonError('No settings');
            }

            $mapping = $settings->fields;

			$sess = JFactory::getSession();
			$file = $sess->get('list.' . $currentList . '.file.uploaded', array());

            $filename = $file['file']['filepath'];
            $statePath = 'com_newsletter.'.md5('list.'.$currentList.'exclude.file.'.$filename);

            // If there is no extarnal offset then use internal from session
            if (!is_numeric($offset)) {
                $offset = $app->getUserState($statePath.'.offset', 0);
            }	

            // If this is a start then init session variables
            if ($offset == 0) {
                $app->setUserState($statePath.'.seek', 0);
                $app->setUserState($statePath.'.skipped', 0);
                $app->setUserState($statePath.'.errors', 0);
                $app->setUserState($statePath.'.unbound', 0);
            }

            // Restore previous state
            $seek     = $app->getUserState($statePath.'.seek', 0);
            $skipped  = $app->getUserState($statePath.'.skipped', 0);
            $errors   = $app->getUserState($statePath.'.errors', 0);
            $unbound  = $app->getUserState($statePath.'.unbound', 0);

            // Try to open file
            if (($handle = fopen($filename, "r")) === FALSE) {
                NewsletterHelperNewsletter::jsonError('Cannot open file');
            }

            //get the header and seek to previous position
            if ($settings->skipHeader) {
                fgetcsv($handle, 0, $settings->delimiter, $settings->enclosure);
            }	

            // Seek only if SEEK is not on the start to prevent inserting the HEADER into DB
            if ($seek > 0) {
                fseek($handle, $seek);
            }	

            $total = 0;
			
            while (
                ($limit == 0 || $total < $limit) &&
                ($data = fgetcsv($handle, 0, $settings->delimiter, $settings->enclosure)) !== FALSE
            ) {
                $emails[] = array(
                    'email' => $data[$mapping->email->mapped],
                );
				$total++;
            }
            
            // Store seek for further requests and close file
            $app->setUserState($statePath.'.seek', ftell($handle));
            fclose($handle);

            $subscriber = MigurModel::getInstance('subscriber', 'newsletterModel');
            $mList = MigurModel::getInstance('List', 'NewsletterModel');

			$dbo = JFactory::getDbo();
			$dbo->transactionStart();

			$res = array();

            foreach ($emails as $row) {

                $user = $subscriber->getItem(array('email' => $row['email']));

                if ($user) {
                    if($mList->unbindSubscriber($currentList, $user['subscriber_id'])) {
                        $res['unbound']++;
                    } else {
                        $res['errors']++;
                    }
                } else {
                    $res['skipped']++;
                }
            }

			$dbo->transactionCommit();
			
            if (!empty($res['errors'])) {
                NewsletterHelperNewsletter::jsonError(JText::_('COM_NEWSLETTER_EXCLUSION_FAILED'), array(
                    'fetched'   => $total,
                    'total'     => $offset   + $total,
                    'skipped'   => $skipped,
                    'errors'    => $errors   + $res['errors'],
                    'unbound'   => $unbound  + $res['unbound']
                ));
            }

            // Check if this is not the end
            if ($total > 0) {

                // Store offsets and stats
                $app->setUserState($statePath.'.offset', $offset + $total);
                $app->setUserState($statePath.'.skipped', $skipped);
                $app->setUserState($statePath.'.errors', $errors + $res['errors']);
                $app->setUserState($statePath.'.unbound', $unbound + $res['unbound']);

                NewsletterHelperNewsletter::jsonMessage('ok', array(
                    'fetched'   => $total,
                    'total'     => $offset   + $total,
                    'skipped'   => $skipped,
                    'errors'    => $errors + $res['errors'],
                    'unbound'   => $unbound  + $res['unbound'],
                ));
            }

            // This is the end
            $app->setUserState($statePath.'.seek', 0);
            $app->setUserState($statePath.'.offset', 0);
            $app->setUserState($statePath.'.skipped', 0);
            $app->setUserState($statePath.'.errors', 0);
            $app->setUserState($statePath.'.unbound', 0);

            unlink($file['file']['filepath']);
            $sess->clear('list.' . $currentList . '.file.uploaded');

            $res = array(
                'fetched'   => $total,
                'total'     => $offset + $total,
                'skipped'   => $skipped,
                'total'     => $offset + $total,
                'errors'    => $errors + $res['errors'],
                'unbound'   => $unbound  + $res['unbound']
            );


            NewsletterHelperLog::addDebug(
                'Import successfull. List ID is '.$currentList, 
                NewsletterHelperLog::CAT_EXCLUDE, 
                $res
            );

            NewsletterHelperNewsletter::jsonMessage(JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'), $res);
		}
	}

	
	
	/**
	 * Retrieves the settings from the request data
	 * @return object - data
	 * @since 1.0
	 */
	protected function _getSettings($data)
	{
		$data = (object) $data;
		
		if (empty($data->enclosure) || $data->enclosure == 'no') {
			$data->enclosure = "\0";
		}


		if (empty($data->delimiter)) {
			echo json_encode(array('status' => '0', 'error' => 'Some settings are absent'));
			return false;
		}

		switch ($data->delimiter) {
			case 'tab':
				$data->delimiter = "\t";
				break;
			case 'space':
				$data->delimiter = " ";
				break;
		}

		return $data;
	}
}

