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
	 * See parent's phpdoc
	 * 
	 * @return  boolean
	 * @since   11.1
	 */
	protected function allowAdd($data = array())
	{
		return
			/* newsletter.add have no similar with core.create
			   because it is not inheritance of J! core.create */
			/* parent::allowAdd($data) && */
			AclHelper::actionIsAllowed('newsletter.add');
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
			AclHelper::actionIsAllowed('newsletter.edit');
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
	
	
	
	/**
	 * Bulk save method for saving of newsletter 
	 * or copying of several ones.
	 * 
	 * @return type 
	 */
	public function save(){
		
		
		$task = JRequest::getString('task');
		
		if (!empty($task) && strpos($task, 'save2copy') !== false) {

			// Actualy on this step will be performed 
			// the checking for NEWSLETTER.ADD permission. It's ok
			if (!$this->allowSave()) {
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
				$this->setMessage($this->getError(), 'error');
				$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

				return false;
			}
			
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
					$data['alias'] = NewsletterHelper::getFreeAlias($data['alias']);
					
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
			
			return parent::save();
			
		} else {
			
			$nsid = JRequest::getVar('newsletter_id', '0');
			$type = JRequest::getVar('task');
			$context = JRequest::getString('context', 'html');

			// save new newsletter (create it) or autosave an existing letter

			$data = JRequest::getVar('jform', array(), 'post', 'array');

			// If the type is not changeable then replace type as now (for success validation).
			if (!empty($nsid)) {

				// Get newsletter's extended info 
				$nl = NewsletterHelper::get($nsid);

				if (!$nl['type_changeable']) {
					$data['type'] = $nl['type'];
					JRequest::setVar('jform', $data, 'post');
				}

				// Check if we can change the newsletter
				if (!$nl['saveable']) {

					$error = JText::_('COM_NEWSLETTER_CANNOT_SAVE_NEWSLETTER');	

					if ($context == 'json') {

						echo json_encode(array(
							'state' => $error,
							'newsletter_id' => $nsid
							));
						jexit();
					} else {
						JFactory::getApplication()->enqueueMessage($error, 'error');
						$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=newsletter&layout=edit&newsletter_id='.$nsid, false));
						return;
					}	
				}

			} else {

				if (empty($data['alias'])) {
					$data['alias'] = 'newsletter';
				}

				// Get newsletters with similar aliases
				$data['alias'] = NewsletterHelper::getFreeAlias($data['alias']);
			}


			if (parent::save()) {

				$nsid = $this->newsletterId;

				$htmlTpl = (object) json_decode($data['htmlTpl']);
				$plugins = (array) json_decode($data['plugins']);
				$htmlTpl->extensions = array_merge($htmlTpl->extensions, $plugins);
				$newExtsModel = $this->getModel('newsletterext');
				if ($newExtsModel->rebindExtensions(
						$htmlTpl->extensions,
						$nsid
				)) {

				} else {
					$error = $newExtsModel->getError();
				}
			} else {
				$error = $this->getError();
			}

			if ($context == 'json') {
				
				$this->setRedirect(null);
				if (empty($error)) {
					$error = JFactory::getApplication()->getMessageQueue();
				}

				echo json_encode(array(
					'state' => (!empty($error)) ? $error : 'ok',
					'newsletter_id' => $nsid, 
					'alias' => $data['alias']
				));

				jexit();
			}	
		}
	}

	/**
	 * Saves the Id of current record to give
	 * access for other methods of controller to it
	 *
	 * @param <type> $model - the model object
	 * @param <type> $data  - saved data.
	 * 
	 * @return void
	 * @since 1.0
	 */
	protected function postSaveHook($model, $data)
	{
		$this->newsletterId = $model->getState($model->getName() . '.id');
	}
}
