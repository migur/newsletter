<?php

/**
 * The mail sender. Allow to setup and send the letter via SMTP.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('JPATH_BASE') or die;

jimport('phpmailer.phpmailer');
jimport('joomla.mail.helper');
jimport('migur.library.mailer.transport.smtp');
jimport('joomla.mail.mail');
jimport('joomla.error.log');

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Framework
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurMailerSender extends JMail
{

	/**
	 * The constructor of a class
	 *
	 * @param	array $config array( emails => array, letter => content, smtpProfile => data)
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($params = null)
	{

		if ($params) {
			$this->_setData($params);
		}

		parent::__construct();
	}

	/**
	 * Common setup method.
	 *
	 * @param array $params - the parameters
	 *
	 * @return void
	 * @since  1.0
	 */
	public function _setData($params)
	{
		$this->emails = !empty($params['emails']) ? $params['emails'] : null;
		$this->letter = !empty($params['letter']) ? $params['letter'] : null;
		$this->attach = !empty($params['letter']) ? $params['attach'] : null;
		$this->type = !empty($params['type']) ? $params['type'] : null;

		if (!empty($params['smtpProfile'])) {
			$this->smtpProfile = $params['smtpProfile'];
			$this->setSMTP($params['smtpProfile']);
		}
	}

	/**
	 * Method to setup the SMTP settings.
	 *
	 * @param object $profile - the SMTP settings
	 *
	 * @return void
	 * @since 1.0
	 */
	public function setSMTP($profile)
	{
		$auth = empty($profile->username) ? null : 'auth';

		$this->useSMTP(
			$auth,
			$profile->smtp_server,
			$profile->username,
			$profile->password,
			($profile->is_ssl == 0) ? false : 'ssl',
			$profile->smtp_port
		);
	}

	/**
	 * The main method for letter sending.
	 *
	 * @param array $params - the configuration of letter
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function send($params = null)
	{
		if ($params) {
			$this->_setData($params);
		}

		parent::ClearAddresses();
		parent::setSender(array($this->smtpProfile->from_email, $this->smtpProfile->from_name));
		parent::setSubject($this->letter->subject);

		parent::setBody($this->letter->content);
		foreach($this->attach as $item) {
			$parts = explode(DS, $item->filename);
			$full  = JPATH_ROOT.DS.$item->filename;
			if(function_exists('mime_content_type')) {
				$mime = mime_content_type($full);
			} else {
				$finfo = finfo_open(FILEINFO_MIME);
				$mime = finfo_file($finfo, $full);
				finfo_close($finfo);
				list($mime) = explode(';', $mime);
			}
			parent::AddAttachment($full, '', 'base64', $mime);
		}	
		parent::IsHTML($params['type'] == 'html');
		foreach ($this->emails as $email) {
			if (!empty($email->email)) {
				parent::addAddress($email->email, !empty($email->name) ? $email->name : "");
			}
		}
		if (parent::Send() !== true) {
			JLog::getInstance()->addEntry(array('comment' => 'mailer.sender: ' . JError::getError()->get('message')));
			return false;
		}
		return true;
	}
}