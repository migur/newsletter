<?php

/**
 * The controller for list json requests.
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
	 * Handles the uploading of list (exclude/include)
	 * @since 1.0
	 * @return void
	 */
	public function upload()
	{

		$uploader = JModel::getInstance('file', 'NewsletterModel');
		$data = $uploader->upload(array('overwrite' => true));
		$listId = JRequest::getInt('list_id', 0);

		if (!empty($data['file'])) {
			$sess = JFactory::getSession();
			$sess->set('list.' . $listId . '.file.uploaded', $data);

			$arr = file($data['file']['filepath']);
			$data['fields'] = explode(',', $arr[0]);
		}

		echo json_encode($data);
	}

	/**
	 * Retrieves only first row(names of columns) of the list
	 * @since 1.0
	 * @return void
	 */
	public function gethead()
	{
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
				echo json_encode(array('status' => '0', 'error' => 'Cannot open file'));
				return false;
			}
		} else {
			echo json_encode(array('status' => '0', 'error' => 'No data about file'));
			return false;
		}

		echo json_encode($data);
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

	/**
	 * Fetches and adds the data to DB from the file uploaded before
	 * @return void
	 * @since 1.0
	 */
	public function import()
	{
		$type        = JRequest::getString('subscriber_type', '');
		$subtask     = JRequest::getString('subtask', '');
		$currentList = JRequest::getInt('list_id', '0');
		
		if ($currentList < 1) {
            NewsletterHelper::jsonError('No list Id');
		}

		if (!$settings = $this->_getSettings()) {
            NewsletterHelper::jsonError('No settings');
		}

		if ($subtask == 'parse') {

			$mapping = $settings->fields;

			$sess = JFactory::getSession();
			$file = $sess->get('list.' . $currentList . '.file.uploaded', array());

			if (($handle = fopen($file['file']['filepath'], "r")) === FALSE) {
                
                NewsletterHelper::jsonError('Cannot open file');
                
			}

            $res = array();
            $total = 0;
            $skipped = 0;

            //get the header
            fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure);

            while (($data = fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure)) !== FALSE) {
                if ($mapping->html->mapped === null || !isset($data[$mapping->html->mapped])) {
                    $htmlVal = $mapping->html->default;
                } else {
                    $htmlVal = $data[$mapping->html->mapped];
                }

                if (!empty($data[$mapping->username->mapped]) && !empty($data[$mapping->email->mapped])) {
                    $res[] = array(
                        'name' => $data[$mapping->username->mapped],
                        'email' => $data[$mapping->email->mapped],
                        'html' => $htmlVal
                    );
                } else {
                    $skipped++;
                }

                $total++;
            }
            fclose($handle);

            $subscriber = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

            $errors    = 0;
            $added     = 0;
            $updated   = 0;
            $assigned  = 0;
            foreach ($res as $row) {

                $success = true;

                // Try to load a man
                $isExists = $subscriber->load(array('email' => $row['email']));

                // Set confirmed is it's empty
                if (!$subscriber->confirmed == 0) {
                    $subscriber->confirmed = 1;
                }

                if (!$isExists) {
                    // If user is not exists then add it!
                    $success = $subscriber->save($row, $type == 'juser');
                    $added++;

                } else {

                    if ($settings->overwrite) {
                        // If user is present and we can update it...
                        $success = $subscriber->save($row);
                        $updated++;

                    }	
                }

                if ($subscriber->getId() && $success) {

                    // Assign the man only if he is not in list already
                    if(!$subscriber->isInList($currentList)) {
                        if($subscriber->assignToList($currentList)) {

                            $assigned++;

                        } else {

                            $errors++;
                        }
                    }	

                } else {

                    $errors++;
                }
            }

            if (!empty($errors)) {
                NewsletterHelper::jsonError('Import failed!', array(
                    'total'   => $total,
                    'skipped' => $skipped,
                    'errors'  => $errors,
                    'added'   => $added,
                    'updated' => $updated,
                    'assigned'=> $assigned));
            }
            
            unlink($file['file']['filepath']);
            $sess->clear('list.' . $currentList . '.file.uploaded');

            NewsletterHelper::jsonMessage('Import complete!', array(
                'total'   => $total,
                'skipped' => $skipped,
                'errors'  => $errors,
                'added'   => $added,
                'updated' => $updated,
                'assigned'=> $assigned));
		}
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

			$mSub = JModel::getInstance('subscriber', 'newsletterModel');
			$total = count($subscribers);
			foreach ($subscribers as $item) {
				$res = $mSub->unbindFromList((object) array(
							'subscriber_id' => $item,
							'list_id' => $currentList
					));

				if (!$res) {

					echo json_encode(array(
						'status' => 0,
						'error' => JText::_('COM_NEWSLETTER_EXCLUSION_FAILED'),
						'total' => $total
					));
					return;
				}
			}

			echo json_encode(array(
				'status' => 1,
				'error' => JText::_('COM_NEWSLETTER_EXCLUSION_COMPLETE'),
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
				fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure);

				while (($data = fgetcsv($handle, 1000, $settings->delimiter, $settings->enclosure)) !== FALSE) {
					$res[] = array(
						'email' => $data[$mapping->email->mapped],
					);
				}
				fclose($handle);

				$subscriber = JModel::getInstance('subscriber', 'newsletterModel');

				$total = count($res);
				$absent = 0;
				$processed = 0;
				foreach ($res as $row) {

					$user = $subscriber->getItem(array('email' => $row['email']));

					if ($user) {
						$res = $subscriber->unbindFromList((object) array(
									'subscriber_id' => $user->subscriber_id,
									'list_id' => $currentList
							));

						if (!$res) {
							echo json_encode(array(
								'status' => 0,
								'error' => 'Import failed!',
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

				echo json_encode(array(
					'status' => 1,
					'error' => 'Excluding complete!',
					'processed' => $processed,
					'absent' => $absent,
					'total' => $total
				));
				return;
			}
		}
	}
}

