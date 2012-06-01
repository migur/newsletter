<?php

/**
 * The list model. Implements the standard functional for list view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of the list model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelList extends JModelAdmin
{
	protected $_mailer;
	
	protected $_context;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'List', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_newsletter.list', 'list', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.list.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	public function getSubscribers($listId)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__newsletter_sub_list AS a');
		$query->join('', '#__newsletter_subscribers AS s ON a.subscriber_id=s.subscriber_id');
		$query->where('a.list_id=' . intval($listId));
		// echo nl2br(str_replace('#__','jos_',$query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
    
    /**
     * Import subscribers into list.
     * Creates Migur or Joomla user if necessary
     *
     * TODO: Better to place it in something like NewsletterManagerList...
     * 
     * @param type $collection List of objects
     * @param type $options Lsit of options
     * @return type Result data array
     */
    public function importCollection($listId, $collection, $options)
    {
        $subscriber = JModel::getInstance('Subscriber', 'NewsletterModelEntity');

        $errors    = 0;
        $added     = 0;
        $updated   = 0;
        $assigned  = 0;
        $skipped   = 0;
        
        $errorOnFail = isset($options['errorOnFail'])? (bool)$options['errorOnFail'] : false;
        
        foreach ($collection as $row) {
            
            $row = (array)$row;
            
            $success = true;
            $isExists = false;

            // Try to load J! user first if id is provided
            if (!empty($row['id'])) {
                $isExists = $subscriber->load('-'.$row['id']);
            }
            
            // If fail then Try to load a man by email
            if (!$isExists) {
                
                // No email. Skip this record or throw an error
                if(empty($row['email'])) {
                    if ($errorOnFail) {
                        throw new Exception('Import of a subscriber failed! No email. Name:'.$row['name']);
                    }    
                    else {
                        $skipped++;
                        continue;
                    }    
                }
                
                $isExists = $subscriber->load(array('email' => $row['email']));
            }    

            // Set confirmed if it's empty
            if (!$subscriber->confirmed == 0) {
                $subscriber->confirmed = 1;
            }

            if (!$isExists) {
                
                // If user is not exists then add it!
                // Can create ONLY subscribers, NOT J!USERS.
                if ($success = $subscriber->save($row)) {
                    $added++; 
                }

            } else {
                
                // If user is present and we can update it
                // Then do it but not for J! Users
                if ($options['overwrite'] && 
                    !$subscriber->isJoomlaUserType() && 
                    $success = $subscriber->save($row)
                ) {
                    $updated++;
                }	
                
            }

            if ($subscriber->getId() && $success) {

                // Assign the man only if he is not in list already
                if(!$subscriber->isInList($listId)) {
                    if($subscriber->assignToList($listId)) {

						// Send subscription letter. But not immediately.
						// Just add in queue
						$res = $this->sendSubscriptionMail(
							$subscriber, 
							$listId,
							array(
								'addToQueue'       => true,
								'ignoreDuplicates' => true)
						);
						
						if ($res) {
							
							// Fire event onMigurAfterSubscriberImport
							JFactory::getApplication()->triggerEvent('onMigurAfterSubscriberImport', array(
								'subscriberId' => $subscriber->getId(),
								'lists' => array($listId)
							));

							$assigned++;
							
						} else {
							
	                        $errors++;
						}	

                    } else {

                        $errors++;
                    }
                }	

            } else {

                $errors++;
            }
        }


        return array(
            'errors'   => $errors,
            'added'    => $added,
            'updated'  => $updated,
            'assigned' => $assigned,
            'skipped'  => $skipped
        );
    }
	
	function sendSubscriptionMail($subscriber, $listId, $options = array()) 
	{		
		JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR);
		jimport('migur.library.mailer');
		
		// Get list
		$table = $this->getTable();
		$table->load($listId);
		$list = (object) $table->getProperties();
		
		// Check if we need to use fallback newsletter if 
		// newsletter on subscription is not defined
		if (!empty($options['noFallback']) && empty($list->send_at_reg)) {
			// True means no errors...
			return true;
		}

		// Get subscriber
		if (!empty($subscriber) && is_numeric($subscriber)) {
			$subscriber = JModel::getInstance('Subscriber', 'NewsletterModelEntity');
			$subscriber->load($subscriber);
		}
		
		// Get newsletter to send
		$newsletter = JModel::getInstance('Newsletter', 'NewsletterModelEntity');
		$newsletter->loadAsWelcoming($list->send_at_reg);
		
		if (!$subscriber->getId() || !$newsletter->getId() || empty($list->list_id)) {
			throw new Exception('Missing required options');
		}

		$queueManager = JModel::getInstance('Queues', 'NewsletterModel');
		// Return if no need to send duplicaded mails
		if (
			empty($options['ignoreDuplicates']) &&
			$queueManager->isMailExist($subscriber->getId(), $newsletter->getId())
		) {
			return true;
		}	
		
		// Check if we need to send it immediately or just store it in queue
		if (!empty($options['addToQueue'])) {
			
			return $queueManager->addMail(
				$subscriber->getId(),
				$newsletter->getId(),
				$list->list_id);
			
		}
		
		
		// Let's send wellcoming letter
		try {
			
			PlaceholderHelper::setPlaceholder('listname', $list->name);
			
			if (!$this->_mailer) {
				$this->_mailer = new MigurMailer();
			}	
			
			$res = $this->_mailer->send(array(
				'type'          => $newsletter->isFallback() ? 'plain' : $subscriber->getType(),
				'subscriber'    => $subscriber->toObject(),
				'newsletter_id' => $newsletter->newsletter_id,
				'tracking'      => isset($options['tracking'])? $options['tracking'] : true,
				'useRawUrls'    => isset($options['useRawUrls'])? $options['useRawUrls'] : NewsletterHelper::getParam('rawurls') == '1'
			));

			if (!$res->state) {
				throw new Exception(json_encode($res->errors));
			}
			
			LogHelper::addMessage(
				'COM_NEWSLETTER_WELLCOMING_NEWSLETTER_SENT_SUCCESSFULLY', LogHelper::CAT_SUBSCRIPTION, array('Email' => $subscriber->email, 'Newsletter' => $newsletter->name));

		} catch (Exception $e) {
			LogHelper::addError(
				'COM_NEWSLETTER_WELCOMING_SEND_FAILED', LogHelper::CAT_SUBSCRIPTION, array(
				'Error' => $e->getMessage(),
				'Email' => $subscriber->email,
				'Newsletter' => $newsletter->name));
			return false;
		}
		
		return true;
	}
	
}
