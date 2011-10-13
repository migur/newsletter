<?php

/**
 * The controller for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

JLoader::import('tables.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerNewsletter extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
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
		//TODO: Remove and check
		return true;
	}

	/**
	 * Method override to check if you can add an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0
	 */
	protected function allowAdd($data = array(), $key = 'id')
	{
		//TODO: Remove and check
		return true;
	}

	/**
	 * Creates the letter for a preview
	 *
	 * @return void
	 * @since  1.0
	 */
	public function preview()
	{
		$data = JRequest::get();
		$mailer = new MigurMailer();
		echo $mailer->render($data);
	}
	
	public function save(){
		
		$task = JRequest::getString('task');
		
		if (!empty($task) && strpos($task, 'save2copy') !== false) {
			
			$nIds = JRequest::getVar('cid', array());
			
			if (!empty($nIds)) {
				
				$table = JTable::getInstance('Newsletter', 'NewsletterTable');
				$relTable = JTable::getInstance('Newsletterext', 'NewsletterTable');
				$downTable = JTable::getInstance('Downloads', 'NewsletterTable');
				
				foreach($nIds as $nId) {
					
					// Copying the newsletter...
					$table->load($nId);
					$data = $table->getProperties();
					
					// reset
					$table->reset();
					$table->set($table->getKeyName(), null);
					unset($data['newsletter_id']);
					$data['name'] .= '(copy)';
					$data['sent_started'] = '';
					
					// bind
					if (!$table->bind($data)) {
						$this->setError($table->getError());
						return false;
					}
					
					// Store data.
					if (!$table->store()) {
						$this->setError($table->getError());
						return false;
					}
					
					$newNid = $table->get($table->getKeyName());

					// Copy extensions...
					$exts = $relTable->getRowsBy($nId);
					if (!empty($exts)) {
						foreach($exts as $ext) {

							// reset
							$relTable->reset();
							$relTable->set($relTable->getKeyName(), null);
							unset($ext[$relTable->getKeyName()]);
							$ext['newsletter_id'] = $newNid;
							
							// bind
							if (!$relTable->bind($ext)) {
								$this->setError($relTable->getError());
								return false;
							}
							
							// Store data.
							if (!$relTable->store()) {
								$this->setError($relTable->getError());
								return false;
							}
						}
					}	

					// Copy downloads...
					$exts = $downTable->getRowsBy($nId);
					if (!empty($exts)) {
						foreach($exts as $ext) {

							// reset
							$downTable->reset();
							$downTable->set($downTable->getKeyName(), null);
							unset($ext[$downTable->getKeyName()]);
							$ext['newsletter_id'] = $newNid;
							
							// bind
							if (!$downTable->bind($ext)) {
								$this->setError($downTable->getError());
								return false;
							}
							
							// Store data.
							if (!$downTable->store()) {
								$this->setError($downTable->getError());
								return false;
							}
						}
					}	
					
					// Clean the cache.
					$cache = JFactory::getCache($this->option);
					$cache->clean();
				}
				
				$message = JText::_('COM_NEWSLETTER_NEWSLETTER_COPY_SUCCESS');
				$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=newsletters&form=newsletters', false), $message, 'message');
				return true;
			} else {
				
				$message = JText::_('COM_NEWSLETTER_SELECT_AT_LEAST_ONE_ITEM');
				$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=newsletters&form=newsletters', false), $message, 'error');
				return true;
			}
		}
		
		return parent::save();
	}

}
