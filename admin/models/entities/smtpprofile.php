<?php

/**
 * The SMTP profile model. Implements the standard functional for SMTP profile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of SMTPprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelEntitySmtpprofile extends MigurModel
{
	// This value represents the default profile 
	// in tables. Real ID need to calculate
	const DEFAULT_SMTP_ID = -1;
	
	// This value represents the joomla profile 
	// in tables. Real ID need to calculate
	const JOOMLA_SMTP_ID = 0;
	
	protected $_defaults = array(
		'params' => array(
			'inProcess'           => 0,
			'periodLength'        => 60, // 60 minutes
			'periodStartTime'     => 0,
			'sentsPerLastPeriod'  => 0,
			'sentsPerPeriodLimit' => 100 ));

	
	/**
	 * True if now is time to start new mailing period for this SMTP prof
	 * 
	 * @return boolean
	 */
	public function isNeedNewPeriod()
	{
		return (mktime() - $this->params->periodStartTime) > ($this->params->periodLength * 60);
	}

	
	/**
	 * Return count of mails need to send in this mailing period
	 * 
	 * @return boolean
	 */
	public function needToSendCount()
	{
		return ($this->_data->params->sentsPerPeriodLimit - $this->_data->params->sentsPerLastPeriod);
	}

	
	public function startNewPeriod()
	{
		$this->params->periodStartTime = mktime();
		$this->params->sentsPerLastPeriod = 0;
		$this->params->inProcess = 0;
		return $this->save();
	}

	
	public function isInProcess()
	{
		return $this->_data->params->inProcess == 1;
	}

	
	public function kill()
	{
		$this->_data->params->inProcess = 0;
		$this->save();
	}
	
	public function setInProcess($val = 1)
	{
		$this->params->inProcess = $val;
		return $this->save();
	}

	
	public function updateSentsPerPeriodCount($cnt = 1)
	{
		$this->params->sentsPerLastPeriod += $cnt;
		return $this->save();
	}

	
	public function isJoomlaProfile()
	{
		return !empty($this->_data->is_joomla);
	}

	/**
	 * True if this id matches with Default Smtp Id
	 * or if the default smtpp is the Joomla smtpp
	 * and this smtpp is Jomla one.
	 * 
	 * @return type 
	 */
	public function isDefaultProfile()
	{
		$defId = $this->getDefaultSmtpId();
		
		return 
			($defId == $this->_data->smtp_profile_id) || 
			($defId == 0 && $this->isJoomlaProfile());
	}
	
	/**
	 * Can load default profile or J! profile in addition to standard behavior 
	 * 
	 * @param type $data 
	 */
	public function load($data)
	{
		if(!parent::load($data)) {
			
			if (
				!is_numeric($data) ||
				($data != self::DEFAULT_SMTP_ID &&
				 $data != self::JOOMLA_SMTP_ID)
			) {
				return false;
			}

			// If user wants to load DEFAULT SMTPP then determine it.
			if ($data == self::DEFAULT_SMTP_ID) {
				$data = $this->getDefaultSmtpId();
			}

			// If we determine that need to load J! SMTPP
			// then change filter to load it.
			if ($data == self::JOOMLA_SMTP_ID) {
				$data = array('is_joomla' => 1);
				$isJoomla = true;
			}
			
			if (!parent::load($data)) {
				
				if (empty($isJoomla)) {
					return false;
				}
				
				if(!$this->save((array)$this->_getJoomlaProfile())) {
					return false;
				}
			}
		}	

		// If loaded profile is J! profile
		// then fill it with J! SMTP settings
		if ($this->isJoomlaProfile()) {
			$this->addFromArray($this->_getJoomlaProfile());
		}
		
		if(empty($this->_data->params)) {
			$this->_data->params = (object)$this->_defaults['params'];
			$this->save();
		}

		return true;
	}

	
	/**
	 * Load Joomla SMTP profile
	 * 
	 * @param type $data 
	 */
	public function loadJoomla()
	{
		return $this->load(self::JOOMLA_SMTP_ID);
	}
	
	
	/**
	 * Load default SMTP profile
	 * 
	 * @param type $data 
	 */
	public function loadDefault()
	{
		return $this->load(self::DEFAULT_SMTP_ID);
	}
	
	
	/**
	 * Get SMTP default profile or J! profile if the default is not configured.
	 *
	 * @param string $name - id of a letter
	 *
	 * @return object - list of subscribers
	 * @since 1.0
	 */
	public function getDefaultSmtpId()
	{
		$options = JComponentHelper::getComponent('com_newsletter');
		$options = $options->params->toArray();

		return empty($options['general_smtp_default']) ? 0 : (int) $options['general_smtp_default'];
	}

	/**
	 * Create the "_smtp"-like profile from J! mail settings
	 *
	 * @return JObject
	 */
	protected function _getJoomlaProfile()
	{
		$config = new JConfig();
		$data = JArrayHelper::fromObject($config);

		$res = new stdClass();
		$res->smtp_profile_name = JText::_('COM_NEWSLETTER_JOOMLA_SMTP_PROFILE');
		$res->from_name = $data['fromname'];
		$res->from_email = $data['mailfrom'];
		$res->reply_to_email = $data['mailfrom'];
		$res->reply_to_name = $data['fromname'];
		$res->smtp_server = $data['smtphost'];
		$res->smtp_port = $data['smtpport'];
		$res->is_ssl = (strtolower($data['smtpsecure']) == 'ssl') ? 1 : 0;
		$res->pop_before_smtp = 0;
		$res->username = $data['smtpuser'];
		$res->password = $data['smtppass'];
		$res->mailbox_profile_id = NewsletterTableMailboxprofile::MAILBOX_DEFAULT;
		$res->is_joomla = 1;
		$res->mailer = $data['mailer'];

		return $res;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 *
	 * @return	JTable	A database object
	 * @since	1.0.4
	 */
	public function getTable($type = 'Smtpprofile', $prefix = 'NewsletterTable')
	{
		return JTable::getInstance($type, $prefix);
	}

}
