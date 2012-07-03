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
		if (!$this->allowEdit()) {
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
			return false;
		}
		
		
		if (JRequest::getMethod() == "POST") {

			$subscriber  = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
			$newsletter  = JModel::getInstance('Newsletter', 'NewsletterModelEntity');
			$listManager = JModel::getInstance('List', 'NewsletterModel');
			
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

								if (!$listManager->sendSubscriptionMail(
									$subscriber,
									$listId, 
									array(
										'addToQueue'       => true,
										'ignoreDuplicates' => true))
								){	
									throw new Exception();
								}
								
								if(!$subscriber->assignToList($listId)){
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

			$model = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

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
    
    
    public function importPluginTrigger()
    {
		$pGroup = 'migur';
        $pName = JRequest::getString('pluginname', null);
		$pEvent = JRequest::getString('pluginevent', null);

        JLoader::import('plugins.manager', JPATH_COMPONENT_ADMINISTRATOR, '');

        $manager = NewsletterPluginManager::factory('import');

        // Trigger event for plugin
        $context = $this->option.'.edit.'.$this->context;
        $listId = JRequest::getInt('list_id'); 

        $res = $manager->trigger(
            array(
                'name'  => $pName,
                'group' => $pGroup,
                'event' => $pEvent),
            array(
                $listId,
                (array) JRequest::getVar('jform')
        ));

        // In this case we trigger only one plugin then his data is in first element
        $res = $res[0];
        
        
        // Get VIEW.....
        // Set layout for event
        $pEvent = str_replace('onMigurImport', '', $manager->pluginEvent);
        
		JRequest::setVar('view', 'list');
		JRequest::setVar('layout', $pEvent);
        
		$view = $this->getView(
            'list', 'html', '', 
            array(
                'base_path' => $this->basePath, 
                'layout' => 'import_plugin-'.strtolower($pEvent)
        ));

        // Get all view need...
        $plg = $manager->getPlugin($pName, $pGroup);
        $plugin = new stdClass();
        $plugin->data = (array) $res;
        $plugin->name = (string) $pName;
        $plugin->group = (string) $pGroup;
        $plugin->title = $plg->getTitle();
        
        // Complement data
        $plugin->description = empty($res->description)? $plg->getDescription() : $res['description'];
        
        // Set all view need...
        $view->assignRef('plugin', $plugin);
        $view->assign('listId', $listId);
        
        return $this->display();
    }
	
	
	
	/**
	 * Retrieves only first row(names of columns) of the list
	 * @since 1.0
	 * @return void
	 */
	public function gethead()
	{
		NewsletterHelper::jsonPrepare();
		
		$listId = JRequest::getInt('list_id', 0);

		if (!$settings = $this->_getSettings()) {
			return;
		}

		$sess = JFactory::getSession();
		$data = $sess->get('list.' . $listId . '.file.uploaded', array());
		if (!empty($data['file']['filepath'])) {
			if (($handle = fopen($data['file']['filepath'], "r")) !== FALSE) {
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
		$file = $sess->get('list.' . $currentList . '.file.uploaded', array());

		$filename = $file['file']['filepath'];
		$statePath = 'com_newsletter.list.'.$currentList.'import.file.'.$filename;
		
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
		if (($handle = fopen($filename, "r")) === FALSE) {
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
		fclose($handle);

		// Let's import it all!
		$list = JModel::getInstance('List', 'NewsletterModel');
		$res = $list->importCollection(
			$currentList,
			$collection, 
			array(
				'overwrite' => $settings->overwrite,
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

		unlink($file['file']['filepath']);
		$sess->clear('list.' . $currentList . '.file.uploaded');

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

			$list = JModel::getInstance('list', 'newsletterModel');

			$subscribers = array();

			foreach ($data->lists as $listId) {
				$res = array();
				$subs = $list->getSubscribers($listId);
				foreach ($subs as $item) {
					if (!in_array($item->subscriber_id, $subscribers))
						$subscribers[] = $item->subscriber_id;
				}
			}

			$mList = JModel::getInstance('List', 'NewsletterModel');
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
			$file = $sess->get('list.' . $currentList . '.file.uploaded', array());


			
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

				$subscriber = JModel::getInstance('subscriber', 'newsletterModel');
				$mList = JModel::getInstance('List', 'NewsletterModel');

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
							return;
						} else {
							$processed++;
						}
					} else {
						$absent++;
					}
				}

				unlink($file['file']['filepath']);
				$sess->clear('list.' . $currentList . '.file.uploaded');

				NewsletterHelper::jsonMessage(JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'), array(
					'processed' => $processed,
					'absent' => $absent,
					'total' => $total
				));
				return;
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

