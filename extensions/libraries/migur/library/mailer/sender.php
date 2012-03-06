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
jimport('phpmailer.smtp');
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
class MigurMailerSender extends PHPMailer
{
	protected $_errors;
	
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

		parent::__construct(!empty($params['exceptions']));
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
		$this->fromName  = !empty($params['fromName']) ? $params['fromName'] : null;
		$this->fromEmail = !empty($params['fromEmail']) ? $params['fromEmail'] : null;
		$this->toName  = !empty($params['toName']) ? $params['toName'] : null;
		$this->toEmail = !empty($params['toEmail']) ? $params['toEmail'] : null;
		$this->attach = !empty($params['attach']) ? $params['attach'] : array();
		$this->type = !empty($params['type']) ? $params['type'] : null;

		if (!empty($params['smtpProfile'])) {
			$this->smtpProfile = $params['smtpProfile'];
			$this->setSMTP($params['smtpProfile']);
		}
	}

	/**
	 * TODO: Bad naming. Uses not only for SMTP.
	 * Method to setup the SMTP settings.
	 *
	 * @param object $profile - the SMTP settings
	 *
	 * @return void
	 * @since 1.0
	 */
	public function setSMTP($profile)
	{
		$mailer = !empty($profile->mailer)? $profile->mailer : 'smtp';
		
		switch($mailer) {
		
			case 'mail':
				$this->IsMail();
				return true;
				
			case 'sendmail':	
				$this->IsSendmail();
				return true;
				
			default:
			case 'smtp':
				$auth = empty($profile->username) ? null : 'auth';
				switch($profile->is_ssl) {

					case 1: 
						$secure = 'ssl'; break;

					case 2: 
						$secure = 'tls'; break;

					default: 
						$secure = false;
				}

				$this->SMTPAuth = $auth;
				$this->Host		= $profile->smtp_server;
				$this->Username = $profile->username;
				$this->Password = $profile->password;
				$this->Port		= $profile->smtp_port;

				if ($secure == 'ssl' || $secure == 'tls') {
					$this->SMTPSecure = $secure;
				}

				if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null)
					|| ($this->SMTPAuth === null && $this->Host !== null)) {
					$this->IsSMTP();

					return true;
				}
		}		
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

		$this->ClearAddresses();
		
		$this->AddReplyTo(
			JMailHelper::cleanAddress($this->toEmail),
			JMailHelper::cleanText($this->toName)
		);
		
		$this->SetFrom(
			JMailHelper::cleanAddress($this->fromEmail),
			JMailHelper::cleanText($this->fromName)
		);
		
		$this->Subject = JMailHelper::cleanText($this->letter->subject);

		if (!empty($this->letter->encoding)) {
			$this->CharSet = $this->letter->encoding;
		}	
		
		$this->Body = JMailHelper::cleanText($this->letter->content);
		foreach($this->attach as $item) {
			$parts = explode(DS, $item->filename);
			$full  = JPATH_ROOT.DS.$item->filename;
			if(function_exists('mime_content_type')) {
				$mime = mime_content_type($full);
			} elseif (function_exists('finfo_open')) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mime = finfo_file($finfo, $full);
				finfo_close($finfo);
				list($mime) = explode(';', $mime);
			} else {
				$mime = 'application/octet-stream';
			}	
			
			parent::AddAttachment($full, '', 'base64', $mime);
		}	
		parent::IsHTML($params['type'] == 'html');
		foreach ($this->emails as $email) {
			if (!empty($email->email)) {
				parent::addAddress($email->email, !empty($email->name) ? $email->name : "");
			}
		}
		
		try {
			if (!parent::Send()) {
				throw new Exception();
			}
		} catch(Exception $e) {	

			$msg = $e->getMessage();
			if (!empty($msg)) {
				$this->setError($msg);
			}	
			
			LogHelper::addDebug('Mailer.Sender error.', LogHelper::CAT_MAILER, $this->getErrors());
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Wraps the original CreateHeader to be able to set some headers
	 */
	public function CreateHeader() {
		
		$result = parent::CreateHeader();		
		
		preg_match_all('/Return-Path:[^\n\r]+[\n\r]/', $result, $matches);
		
		if (count($matches[0]) > 1) {
			$start = strpos($result, $matches[0][0]);
			$length = strlen($matches[0][0]);
			$result = substr_replace($result, '', $start, $length);
		}
		
// mail.ru testing headers
//		$result = "Date: Thu, 20 Oct 2011 18:52:09 +0300\r\nReturn-Path:andreyalek-ru@mail.ru\r\nReturn-Receipt-To:andreyalek-ru@mail.ru\r\n".
//					"To:andreyalek-ru@mail.ru\r\nFrom: Andrey <august-ru@mail.ru>\r\n".
//					"Reply-to: Andrey <august-ru@mail.ru>\r\nSubject: Baby Doe\r\n".
//					"Message-ID: <d10f643bf3f7b3bb1303d279ff28f526@migur.woody.php.nixsolutions.com>\r\n".
//					"X-Priority: 3\r\nX-Mailer: PHPMailer 5.1 (phpmailer.sourceforge.net)\r\n".
//					"Email-Name: Birthday of Baby Doe!(copy)\r\nSubscriber-ID: 8316\r\n".
//					"MIME-Version: 1.0\r\nContent-Type: multipart/mixed;boundary=\"b1_d10f643bf3f7b3bb1303d279ff28f526\"\r\n";

		// gmail
//		$result = "Date: Thu, 20 Oct 2011 18:52:09 +0300\r\nReturn-Path:august-ru@mail.ru\r\nReturn-Receipt-To:august-ru@mail.ru\r\n".
//					"To: Woody woody <bounced@nobodydomaintr.com>\r\nFrom: Andrey <andreyalek@gmail.com>\r\n".
//					"Reply-to: Andrey <andreyalek@gmail.com>\r\nSubject: Baby Doe\r\n".
//					"Message-ID: <d10f643bf3f7b3bb1303d279ff28f526@migur.woody.php.nixsolutions.com>\r\n".
//					"X-Priority: 3\r\nX-Mailer: PHPMailer 5.1 (phpmailer.sourceforge.net)\r\n".
//					"Email-Name: Birthday of Baby Doe!(copy)\r\nSubscriber-ID: 8316\r\n".
//					"MIME-Version: 1.0\r\nContent-Type: multipart/mixed;boundary=\"b1_d10f643bf3f7b3bb1303d279ff28f526\"\r\n";

		return $result;
	}
	
	/**
	* Check connection
	* @return bool
	*/
	public function checkConnection($smtpOptions) 
	{
		$this->setSMTP($smtpOptions);
		
		try {
			$res = $this->SmtpConnect();
		} catch(Exception $e) {
			$res = false;
		}	

		if ($res){
			$this->SmtpClose();
		}

		return $res;
	}	
	
	/**
	 * Get last error
	 * 
	 * @return string
	 */
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	 * Adds the error message to the error container.
	 * @access protected
	 * @return void
	 */
	public function setError($msg)
	{
		$this->_errors[] = JText::_($msg);
		return parent::SetError($msg);
	}
}