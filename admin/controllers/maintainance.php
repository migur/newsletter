<?php

/**
 * The controller for automailing view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

class NewsletterControllerMaintainance extends JControllerForm
{

	/**
	 * All checks of environment helper
	 *
	 * @return string json
	 */
	public function checkEnvironment()
	{
		$checks = NewsletterHelperEnvironment::getAvailableChecks();

		$res = array();
		foreach ($checks as $check) {

			// Skip this to check in checkDb()
			if ($check == 'checkUserConflicts')
				continue;


			$rs = (NewsletterHelperEnvironment::$check() == true) ? 2 : 0;

			$res[] = array(
				'check' => JText::_('COM_NEWSLETTER_MAINTAINANCE_' . strtoupper($check)),
				'message' => $rs ? JText::_('COM_NEWSLETTER_SUCCESS') : JText::_('COM_NEWSLETTER_FAILED'),
				'status' => $rs);
		}

		NewsletterHelperNewsletter::jsonMessage('checkEnvironment', $res);
	}

	/**
	 * Gets last used schema from #_schema
	 * 
	 * @return string json
	 */
	public function checkDb()
	{
		$res = array();



		// 1. Check the schema version
		$man = NewsletterHelperNewsletter::getManifest();
		$version = $man->version;
		$schema = NewsletterHelperEnvironment::getLastSchema();

		$sc = (version_compare($schema, $version) >= 0) ? 2 : 0;

		$res[] = array(
			'check' => JText::_('COM_NEWSLETTER_MAINTAINANCE_CHECKSCHEMA'),
			'message' => $schema);



		// 2. Check if all tables are present

		$installFile = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'install.sql');

		// explode whole script to table alter scripts
		$tableAlters = array();
		preg_match_all("/create\s*table\s*[\`\"\']?([\#\_a-zA-Z0-9]+)[\`\"\']?[^;]*/is", $installFile, $tableAlters);

		$dbo = JFactory::getDbo();
		for ($i = 0; $i < count($tableAlters[0]); $i++) {

			// Process each table...
			$tableName = $tableAlters[1][$i];
			$alterScript = $tableAlters[0][$i];

			// Get fields of table from alter script
			$matches = array();
			preg_match_all("/^\s*(?:\`|\"|\')([a-z0-9_]+)/m", $alterScript, $matches);
			$fields = $matches[1];

			// Get destription of a table from DB
			$dbo->setQuery('DESCRIBE ' . $tableName);
			$data = $dbo->loadAssocList();

			// Check if table absent at all
			$error = false;
			if ($data === null) {
				$res[] = array(
					'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_CHECK', $tableName),
					'message' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_ABSENT'),
					'status' => false);
				$error = true;
			} else {

				// Check if some fields of a table absent
				$fieldsPresent = array();
				foreach ($data as $item) {
					$fieldsPresent[] = $item['Field'];
				}

				foreach ($fields as $field) {
					if (!in_array($field, $fieldsPresent)) {
						$res[] = array(
							'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_CHECK', $tableName),
							'message' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_CHECK_FIELD_ABSENT', $field),
							'status' => 0
						);
						$error = true;
					}
				}
			}

			if (!$error) {
				$res[] = array(
					'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_CHECK', $tableName),
					'message' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_TABLE_CHECK_OK'),
					'status' => 2
				);
			}
		}



		// 3. Check conflicts
		$count = NewsletterHelperEnvironment::getConflictsCount();

		$res[] = array(
			'check' => JText::_('COM_NEWSLETTER_MAINTAINANCE_CHECKUSERCONFLICTS'),
			'message' => JText::sprintf('COM_NEWSLETTER_CONFLICTS_FOUND', $count),
			'status' => ($count == 0) ? 2 : 1
		);

		// 4. Remove all died rows
		$dbo = JFactory::getDbo();
		$dbo->setQuery(
				'DELETE FROM #__newsletter_subscribers ' .
				'USING #__newsletter_subscribers ' .
				'LEFT JOIN #__users AS u ON u.id = #__newsletter_subscribers.user_id ' .
				'WHERE #__newsletter_subscribers.email="" AND #__newsletter_subscribers.user_id > 0 AND u.id IS NULL');
		if ($dbo->query()) {

			$diedCnt = $dbo->getAffectedRows();

			$res[] = array(
				'check' => JText::_('COM_NEWSLETTER_MAINTAINANCE_CHECKDIEDROWS'),
				'message' => JText::sprintf('COM_NEWSLETTER_DIED_ROWS_FOUND', $diedCnt),
				'status' => 2
			);
		} else {
			$res[] = array(
				'check' => JText::_('COM_NEWSLETTER_MAINTAINANCE_CHECKDIEDROWS'),
				'message' => JText::_('COM_NEWSLETTER_FAILED'),
				'status' => 0);
		}

		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkDb', $res);
	}


	/**
	 * Checks J! component's extensions
	 * 
	 * @return string json
	 */
	public function checkExtensions()
	{
		$res = array();

		// Subscription module
		$extension = JTable::getInstance('extension');
		$extension->load(array('type' => 'module', 'element' => 'mod_newsletter_subscribe'));
		
		if (empty($extension->extension_id)) {
				$message = JText::_('COM_NEWSLETTER_NOT_FOUND');
				$status  = 0;
		} elseif ($extension->enabled == 0) {
				$message = JText::_('COM_NEWSLETTER_DISABLED');
				$status  = 1;
		} else {
				$message = JText::_('COM_NEWSLETTER_ENABLED');
				$status  = 2;
		}
		$res[] = array(
			'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_CHECK_MODULE', 'mod_newsletter_subscribe'),
			'message' => $message,
			'status'  => $status
		);	
		
		
		// Plugin migurlistsync
		$extension = JTable::getInstance('extension');
		$extension->load(array('type' => 'plugin', 'folder' => 'system', 'element' => 'migurlistsync'));
		
		if (empty($extension->extension_id)) {
				$message = JText::_('COM_NEWSLETTER_NOT_FOUND');
				$status  = 0;
		} elseif ($extension->enabled == 0) {
				$message = JText::_('COM_NEWSLETTER_DISABLED');
				$status  = 0;
		} else {
				$message = JText::_('COM_NEWSLETTER_ENABLED');
				$status  = 2;
		}
		$res[] = array(
			'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_CHECK_PLUGIN', 'migurlistsync'),
			'message' => $message,
			'status'  => $status
		);	
		
		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkDb', $res);
	}

	
	/**
	 * Check connections to ALL smtp servers.
	 * With/without certificate validation
	 * 
	 * @return string json
	 */
	public function checkSmtps()
	{
		$res = array();

		$manager = MigurModel::getInstance('Smtpprofiles', 'NewsletterModel');
		$smtpps = $manager->getAllItems();

		if (!empty($manager)) {

			jimport('migur.library.mailer.sender');
			$sender = new MigurMailerSender();
			$model = MigurModel::getInstance('Smtpprofile', 'NewsletterModelEntity');

			foreach ($smtpps as $smtpp) {

				$model->load($smtpp->smtp_profile_id);

				$connection = $sender->checkConnection($model->toObject());

				$res[] = array(
					'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_CHECKSMTP', $model->smtp_profile_name),
					'message' => $connection ? JText::_('COM_NEWSLETTER_CONNECTION_OK') : JText::_('COM_NEWSLETTER_UNABLE_TO_CONNECT'),
					'status' => $connection ? 2 : 0
				);
			}
		} else {
			$res[] = array(
				'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_NO_SMTPPROFILES'),
				'message' => JText::_('COM_NEWSLETTER_SMTPPROFILES_ABSENT'),
				'status' => 0);
		}

		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkSmtps', $res);
	}
	
	
	/**
	 * Check connections to ALL mailbox servers.
	 * 
	 * @return string json
	 */
	public function checkMailboxes()
	{
		$res = array();

		$manager = MigurModel::getInstance('Mailboxprofiles', 'NewsletterModel');
		$mailboxes = $manager->getAllItems();

		if (!empty($mailboxes)) {

			jimport('migur.library.mailer.mailbox');

			foreach ($mailboxes as $mailboxSettings) {

				$text = '';

				$mailboxSettings = (array) $mailboxSettings;
				$mailbox = new MigurMailerMailbox($mailboxSettings);

				$errors = array();

				if ($mailbox->connect()) {
					$mailbox->close();
				} else {

					$errors[] = JText::_('COM_NEWSLETTER_UNABLE_TO_CONNECT');
					$errors[] = $mailbox->getLastError();

					if (!$mailbox->protocol->getOption('noValidateCert')) {
						$mailbox->protocol->setOption('noValidateCert', true);

						$errors[] = JText::_('COM_NEWSLETTER_TRYING_TO_CONNECT_WITHOUT_CERT');

						if ($mailbox->connect()) {
							$mailbox->close();
							$errors[] = JText::_('COM_NEWSLETTER_OK_CHECK_YOUR_CERT');
						} else {
							$errors[] = JText::_('COM_NEWSLETTER_FAILED') . '. ' . $mailbox->getLastError();
						}
					}
				}

				if (count($errors) > 0) {
					$text .= '<br/>' . implode('<br/>', $errors);
				}

				imap_errors();
				imap_alerts();



				$res[] = array(
					'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_CHECKMAILBOX', $mailboxSettings->mailbox_profile_name),
					'message' => $text,
					'status' => (count($errors) == 0) ? 2 : 0
				);
			}
		} else {
			$res[] = array(
				'check' => JText::sprintf('COM_NEWSLETTER_MAINTAINANCE_NO_MAILBOXES'),
				'message' => '',
				'status' => 1
			);
		}

		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkMailboxes', $res);
	}

	public function checkLicense()
	{
		$info = NewsletterHelperNewsletter::getCommonInfo('without cache');

		$type = ($info->is_valid == "JYES");

		$res = array();
		$res[] = array(
			'check' => JText::sprintf('MOD_UPDATER_SUPPORTED_VERSION'),
			'message' => JText::sprintf($info->is_valid));
		$res[] = array(
			'check' => JText::sprintf('MOD_UPDATER_VERSION'),
			'message' => JText::sprintf($info->current_version));
		$res[] = array(
			'check' => JText::sprintf('MOD_UPDATER_LICENSE'),
			'message' => JText::sprintf($info->license_key));
		$res[] = array(
			'check' => JText::sprintf('MOD_UPDATER_DOMAINCODE'),
			'message' => JText::sprintf($info->domain));
		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkLicense', $res);
	}

	public function checkSystemInfo()
	{
		$sysModel = $this->_getSysModel();
		if (!$sysModel) {
			// Return data
			NewsletterHelperNewsletter::jsonError('Cannot load System model');
		}

		$info = $sysModel->getInfo();

		$res = array();
		$res[] = array(
			'check' => JText::_('COM_ADMIN_PHP_BUILT_ON'),
			'message' => $info['php']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_DATABASE_VERSION'),
			'message' => $info['dbversion']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_DATABASE_COLLATION'),
			'message' => $info['dbcollation']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_PHP_VERSION'),
			'message' => $info['phpversion']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_WEB_SERVER'),
			'message' => $info['server']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_WEBSERVER_TO_PHP_INTERFACE'),
			'message' => $info['sapi_name']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_JOOMLA_VERSION'),
			'message' => $info['version']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_PLATFORM_VERSION'),
			'message' => $info['platform']);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_USER_AGENT'),
			'message' => $info['useragent']);
		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkSysteminfo', $res);
	}

	public function checkJdirectories()
	{
		$sysModel = $this->_getSysModel();
		if (!$sysModel) {
			// Return data
			NewsletterHelperNewsletter::jsonError('Cannot load System model');
		}

		$dirs = $sysModel->getDirectory();

		$res = array();
		foreach ($dirs as $dirname => $obj) {
			$res[] = array(
				'check' => $dirname,
				'message' => $obj['writable'] ? JText::_('COM_NEWSLETTER_SUCCESS') : $obj['message'],
				'status' => $obj['writable'] ? 2 : 0
			);
		}

		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkJdirectories', $res);
	}

	public function checkPhpSettings()
	{
		$sysModel = $this->_getSysModel();
		if (!$sysModel) {
			// Return data
			NewsletterHelperNewsletter::jsonError('Cannot load System model');
		}

		$info = $sysModel->getPhpSettings();

		$res = array();
		$res[] = array(
			'check' => JText::_('COM_ADMIN_SAFE_MODE'),
			'message' => $info['safe_mode'] ? JText::_('JYES') : JText::_('JNO'),
			'status' => $info['safe_mode'] ? 0 : 2);

		$res[] = array(
			'check' => JText::_('COM_ADMIN_OPEN_BASEDIR'),
			'message' => $info['open_basedir'] ? $info['open_basedir'] : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_DISPLAY_ERRORS'),
			'message' => $info['display_errors'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_SHORT_OPEN_TAGS'),
			'message' => $info['short_open_tag'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_FILE_UPLOADS'),
			'message' => $info['file_uploads'] ? JText::_('JYES') : JText::_('JNO'),
			'status' => $info['file_uploads'] ? 2 : 0);

		$res[] = array(
			'check' => JText::_('COM_ADMIN_MAGIC_QUOTES'),
			'message' => $info['magic_quotes_gpc'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_REGISTER_GLOBALS'),
			'message' => $info['register_globals'] ? JText::_('JYES') : JText::_('JNO'));
		
		$res[] = array(
			'check'   => JText::_('COM_ADMIN_OUTPUT_BUFFERING'),
			'message' => $info['output_buffering'] ? JText::_('JYES') : JText::_('JNO'),
			'status'  => $info['output_buffering']? 2:0);
		
		$res[] = array(
			'check' => JText::_('COM_ADMIN_SESSION_SAVE_PATH'),
			'message' => $info['session.save_path'],
			'status'  => $info['session.save_path']? 2:0);
		$res[] = array(
			'check' => JText::_('COM_ADMIN_SESSION_AUTO_START'),
			'message' => $info['session.auto_start'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_XML_ENABLED'),
			'message' => $info['xml'] ? JText::_('JYES') : JText::_('JNO'),
			'status' => $info['xml'] ? 2 : 1);

		$res[] = array(
			'check' => JText::_('COM_ADMIN_ZLIB_ENABLED'),
			'message' => $info['zlib'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_ZIP_ENABLED'),
			'message' => $info['zip'] ? JText::_('JYES') : JText::_('JNO'));

		$res[] = array(
			'check' => JText::_('COM_ADMIN_DISABLED_FUNCTIONS'),
			'message' => $info['disable_functions'] ? $info['disable_functions'] : JText::_('JNO'),
			'status' => $info['disable_functions'] ? 1 : 2);

		$res[] = array(
			'check' => JText::_('COM_ADMIN_MBSTRING_ENABLED'),
			'message' => $info['mbstring'] ? JText::_('JYES') : JText::_('JNO'));
		$res[] = array(
			'check' => JText::_('COM_ADMIN_ICONV_AVAILABLE'),
			'message' => $info['iconv'] ? JText::_('JYES') : JText::_('JNO'));

		// Return data
		NewsletterHelperNewsletter::jsonMessage('checkSysteminfo', $res);
	}

	public function _getSysModel()
	{
		JLoader::import('models.sysinfo', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_admin');
		if (!class_exists('AdminModelSysInfo')) {
			return false;
		}

		JFactory::getLanguage()->load('com_admin');

		return new AdminModelSysInfo();
	}

	protected function _renderObject($data, $level = 0)
	{
		$spaces = "                                                ";
		$res = '';

		if (is_array($data) || is_object($data)) {

			foreach ($data as $key => $value) {

				if (!is_numeric($key)) {
					$res .= substr($spaces, 0, $level * 2) . $key . ':';
					$res .= (is_array($value) || is_object($value)) ? "\n" : '';
				}

				if (!is_object($value) && $key == 'status' && $level > 0) {
					switch ($value) {
						case 0: $value = 'ERROR';
							break;
						case 1: $value = 'WARNING';
							break;
						case 2: $value = 'SUCCESS';
							break;
					}
				}

				$res .= $this->_renderObject($value, $level + 1);
			}

			$res .= "\n";
		} else {

			if (is_bool($data)) {
				$data = ($data) ? 'true' : 'false';
			}

			$res .= substr($spaces, 0, $level * 2) . $data . "\n";
		}

		return $res;
	}

	public function getReport()
	{
		$form = JRequest::getVar('jform');
		$data = json_decode($form['data']);
		$document = $this->_renderObject($data);

		header("Content-Type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Length: " . strlen($document));
		header("Content-Disposition: attachment; filename=newslettter-check-report-" . date('Y-m-d-H-i-s') . '.txt');
		echo $document;
		die;
	}

}

