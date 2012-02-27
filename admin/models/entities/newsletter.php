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

JLoader::import('models.entities.smtpprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of SMTPprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelEntityNewsletter extends MigurModel
{
	const CATEGORY_FALLBACK = 1;
	/**
	 * Load newsletter for sending by subscription
	 * 
	 * @param int|string $data Only NID assumed
	 * @return object
	 */
	public function loadAsWelcoming($data)
	{
		if (!$this->load($data)) {
			$params = JComponentHelper::getParams('com_newsletter');
			$data = (int)$params->get('subscription_newsletter_id');
			// Let's try to load default subscription newsletter
			if (!$this->load($data)) {
				// Default subscription newsletter absent. Get fallback newsltter
				return $this->loadFallBackNewsletter();
			}	
		}
	}
	
	
	/**
	 * Sets time of sent time to current time and stores it.
	 * 
	 * @return boolean
	 */
	public function updateSentTime() 
	{
		if (
			$this->sent_started == '0000-00-00 00:00:00' || 
			strtotime($this->sent_started) <= 0
		) {
			$this->sent_started = date('Y-m-d H:i:s');
			return $this->save();
		}
		
		return false;
	}
	
	
	public function loadFallBackNewsletter()
	{
		$defId = NewsletterModelEntitySmtpprofile::DEFAULT_SMTP_ID;
		
		if (!$this->load(array('category' => self::CATEGORY_FALLBACK))) {

			if(!$this->save(array(
				'name'    => 'Fallback newsletter',
				'subject' => 'You have subscribed for [listname] at [sitename]',
				'alias'   => 'fallbacknewsletter',
				'smtp_profile_id' => $defId,
				't_style_id' => null,
				'plain'    =>
					"Hello!\n".
					"Thank you for subscribing to our newsletter.\n".
					"Please confirm your subscription by clicking this link:\n".
					"[confirmation link]",

				'type' => 1,
				'category' => self::CATEGORY_FALLBACK,
				'params' => (object)array(
					'encoding' => 'utf-8'
				)))
			) {
				return false;
			}

		}
		
		if ($this->_data->smtp_profile_id != $defId) {
			$this->_data->smtp_profile_id = $defId;
			return $this->save();
		}	
		
		return true;
	}
	
	
	public function isFallback() 
	{
		return $this->_data->category == self::CATEGORY_FALLBACK;
	}

	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 *
	 * @return	JTable	A database object
	 * @since	1.0.4
	 */
	public function getTable($type = 'Newsletter', $prefix = 'NewsletterTable')
	{
		return JTable::getInstance($type, $prefix);
	}
	
}
