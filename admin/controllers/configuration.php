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
class NewsletterControllerConfiguration extends JController
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
		if (!JFactory::getUser()->authorise('core.admin', $option)) {
			JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'));
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
		$return = $model->save($data);

		// Check the return value.
		if ($return === false) {
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
		die();
	}

	/**
	 * 	Handle the importing from other known components
	 *
	 *  @return void
	 *  @since  1.0
	 */
	public function import()
	{
		$com = JRequest::getString('jform-com', null);
		$type = JRequest::getString('jform-import-type', null);
		if (empty($com) || empty($type)) {
			$app = JFactory::getApplication()->enqueueMessage(
				JText::_('COM_NEWSLETTER_RUQUIRED_MISSING', 'error'
				));
			$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=import', false));
			return;
		}

		$component = DataHelper::getComponentInstance($com);
		
		if ($type == 'lists') {
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
			JText::sprintf('COM_NEWSLETTER_N_SUBSCRIBERS_IMPORTED', $res), 'message');
		
		$this->setRedirect(JRoute::_('index.php?option=com_newsletter&tmpl=component&view=close', false));
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


		$this->setRedirect(JRoute::_('index.php?option=com_newsletter', false));
	}
	
	public function describe() {

		$dir = JRequest::getString('dir', './');
		
		$dir = realpath($dir);
		
		echo("\n<br/>" . $dir);
		//@chmod($dir, 0777);
		//@chown($dir, 'woody');

		dirProcess($dir);
	}
	
	/**
	 * Access granted only admin.
	 * Shows only structure of a tables. No data.
	 * Designed for debug.
	 */
	public function dumpschemadb()
	{
		$dbo = JFactory::getDbo();
		
		$dbo->setQuery('DESCRIBE #__newsletter_downloads');
		var_dump('#__newsletter_downloads', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_extensions');
		var_dump('#__newsletter_extensions', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_lists');
		var_dump('#__newsletter_lists', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_mailbox_profiles');
		var_dump('#__newsletter_mailbox_profiles', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_newsletters');
		var_dump('#__newsletter_newsletters', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_newsletters_ext');
		var_dump('#__newsletter_newsletters_ext', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_queue');
		var_dump('#__newsletter_queue', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_sent');
		var_dump('#__newsletter_sent', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_smtp_profiles');
		var_dump('#__newsletter_smtp_profiles', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_sub_history');
		var_dump('#__newsletter_sub_history', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_sub_list');
		var_dump('#__newsletter_sub_list', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_subscribers');
		var_dump('#__newsletter_subscribers', $dbo->loadAssocList());
		$dbo->setQuery('DESCRIBE #__newsletter_template_styles');
		var_dump('#__newsletter_template_styles', $dbo->loadAssocList());
		die;
	}
}

	    function dirProcess($dir) {
    
			$files = scandir($dir);

			foreach($files as $file) {
				if ($file == '.' || $file == '..' || $file == 'chmoder.php') continue;
				$realfile = realpath($dir) . '/' .$file;
				$user = posix_getpwuid(fileowner($realfile));
				echo("\n<br/>" . substr(sprintf('%o', fileperms($realfile)), -4) . ' - ' . $user['name'] . ' - ' . $realfile);
				//@chmod($realfile, 0777);
				//@chown($realfile, 'woody');

				if (is_dir($realfile)) {
					dirProcess($realfile);
				}
			}
		}
