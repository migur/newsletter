<?php

/**
 * The controller for configuration view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');

// no direct access
defined('_JEXEC') or die;

/**
 * @since 1.0
 */
class NewsletterControllerConfiguration extends MigurController
{
	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @return	void
	 * @since	1.0
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
	}

	/**
	 * 	Save the configuration to component
	 *
	 *  @return void
	 *  @since  1.0
	 */
	function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set FTP credentials, if given.
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('configuration');
		$form = $model->getForm();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$id = JRequest::getInt('id');
		$option = "com_newsletter";

		// Check if the user is authorized to do this.
		if (!AclHelper::canConfigureComponent()) {
			AclHelper::redirectToAccessDenied();
			return;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				// TODO deprecated since 12.1 Use PHP Exception
				if (JError::isError($errors[$i])) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_newsletter.config.global.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=configuration', false));
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		
		$newsletter = MigurModel::getInstance('Newsletter', 'NewsletterModelEntity');
		$newsletter->loadFallBackNewsletter();
		$newsletter->subject = $data['params']['confirm_mail_subject'];
		$newsletter->plain = $data['params']['confirm_mail_body'];
		
		$return2 = $newsletter->save();

		unset($data['params']['confirm_mail_subject']);
		unset($data['params']['confirm_mail_body']);

		$data['option'] = 'com_newsletter';
		$return = $model->save($data);

		// Check the return value.
		if ($return2 === false || $return === false) {
			// Save the data in the session.
			$app->setUserState('com_newsletter.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_newsletter&view=configuration', $message, 'error');
			return false;
		}

		// Set the redirect based on the task.
		switch ($this->getTask()) {
			case 'apply':
				$message = JText::_('COM_NEWSLETTER_SAVE_SUCCESS');
				$this->setRedirect('index.php?option=com_newsletter&view=configuration', $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php?option=com_newsletter&view=configuration');
				break;
		}

		JFactory::getCache('com_newsletter')->clean();
		$this->licDontCheck = true;
		return true;
	}

	/**
	 * 	Handle the exporting from other known components
	 *
	 *  @return void
	 *  @since  1.0
	 */
	public function export()
	{

		$data = DataHelper::exportListsCSV();

		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Length: " . strlen($data));
		header("Content-Disposition: attachment; filename=newslettter-data-export-" . date('Y-m-d-H-i-s') . '.csv');
		echo $data;
		die;
	}

	/**
	 * 	Handle the importing from other known components
	 *
	 *  @return void
	 *  @since  1.0
	 */
	public function import()
	{
		$iterative = JRequest::getBool('iterative', false);
		$limit = JRequest::getInt('limit', 1000);
		$offset = JRequest::getVar('offset', '');
		
		$com = JRequest::getString('jform-com', null);
		$type = JRequest::getString('jform-import-type', null);

		JLoader::import('models.import.common', JPATH_COMPONENT_ADMINISTRATOR);
		
		if ($iterative) {

			NewsletterHelper::jsonPrepare();
			
			if (empty($com) || empty($type)) {
				NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_RUQUIRED_MISSING'));
			}

			$component = NewsletterModelImportCommon::getInstance($com);
			

			if ($type == 'lists') {

				// If there is no extarnal offset thern use internal from session
				if (!is_numeric($offset)) {
					$offset = JFactory::getApplication()->getUserState(
						'com_newsletter.import.'.strtolower(get_class($component)).'.lists.offset', 0
					);
				}	
				
				$arr = $component->exportLists($offset, $limit);
			}	
			
			if ($arr === false) {
				NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_IMPORT_ERROR'));
			}

			$res = $component->importLists($arr);

			if ($res === false) {
				NewsletterHelper::jsonError(JText::_('COM_NEWSLETTER_IMPORT_ERROR'));
			}

			// Part imported ok. Let's save pointer for future.
			$fetched = count($arr);

			JFactory::getApplication()->setUserState(
				'com_newsletter.import.'.strtolower(get_class($component)).'.lists.offset', $offset + $fetched
			);

			// Send responce and finish
			NewsletterHelper::jsonMessage(
				JText::_('COM_NEWSLETTER_IMPORT_SUCCESSFUL'), array(
					'limit'   => $limit,
					'offset'  => $offset,
					'fetched' => $fetched,
					'total'   => $offset + $fetched
			));
			
		} else {
			
			if (empty($com) || empty($type)) {
				$app = JFactory::getApplication()->enqueueMessage(
					JText::_('COM_NEWSLETTER_RUQUIRED_MISSING', 'error'
					));
				$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import', false));
				return;
			}

			$component = NewsletterModelImportCommon::getInstance($com);

			
			if ($type == 'lists') {

				// Prevent non-consequent importing...
				JFactory::getApplication()->setUserState(
					'com_newsletter.import.'.strtolower(get_class($component)).'.lists.offset', 0
				);
				
				$arr = $component->exportLists();
			}

			if ($arr === false) {
				$app = JFactory::getApplication()->enqueueMessage(
					JText::_('COM_NEWSLETTER_IMPORT_ERROR', 'error'
					));
				$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import', false));
				return;
			}

			$res = $component->importLists($arr);

			if ($res === false) {
				$app = JFactory::getApplication()->enqueueMessage(
					JText::_('COM_NEWSLETTER_IMPORT_ERROR', 'error'
					));
				$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import', false));
				return;
			}

			$app = JFactory::getApplication()->enqueueMessage(
				JText::_('COM_NEWSLETTER_IMPORT_SUCCESSFUL') . '. ' . 
				JText::sprintf('COM_NEWSLETTER_N_SUBSCRIBERS_IMPORTED', $res['added'], $res['assigned'], $res['errors']), 'message');

			$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=close', false));
		}	
	}

	/**
	 * 	Init the component. First action after installing the component
	 *
	 *  @return void
	 *  @since  1.0
	 */
	public function init()
	{

		if (JRequest::getInt('delete_backups') == 1) {

			$sess = JFactory::getSession();
			$backups = $sess->get('com-newsletter-backup');

			if (!empty($backups)) {
				$dbo = JFactory::getDbo();
				$dbo->setQuery('SET foreign_key_checks = 0;');
				$dbo->query();

				foreach ($backups as $table) {
					$dbo->setQuery('DROP TABLE IF EXISTS `' . $table['backup'] . '`');
					$dbo->query();
				}

				$dbo->setQuery('SET foreign_key_checks = 1;');
				$dbo->query();
			}

			$sess->set('com-newsletter-backup', null);
		}
        
        
        // Let's init the reating to read/create row in DB for J! SMTP profile.
        $smtp = JModel::getInstance('Smtpprofile', 'NewsletterModelEntity');
        $smtp->load(NewsletterModelEntitySmtpprofile::JOOMLA_SMTP_ID);
        
        
		// Then go to dash
        $this->setRedirect(JRoute::_('index.php?option=com_newsletter', false));
	}
    
}
