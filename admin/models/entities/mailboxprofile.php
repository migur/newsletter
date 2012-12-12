<?php

/**
 * The Mailbox profile model. Implements the standard functional for Mailbox profile view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

JLoader::import('tables.mailboxprofile', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of Mailboxprofile model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelEntityMailboxprofile extends MigurModel
{
	protected $_defaults = array(
		'params' => array());

	
	/**
	 * True if this id matches with Default Mailbox Id
	 * or if the default Mailboxp is the Joomla Mailboxp
	 * and this Mailboxp is Jomla one.
	 * 
	 * @return type 
	 */
	public function isDefault()
	{
		return $this->getId() && $this->getDefaultId() == $this->getId();
	}
	
	/**
	 * Can load default profile or J! profile in addition to standard behavior 
	 * 
	 * @param type $data 
	 */
	public function load($data)
	{
		if(parent::load($data)) {
			return true;
		}	
			
		// If user wants to load DEFAULT MailboxP then determine it.
		if (!is_numeric($data) || $data != NewsletterTableMailboxprofile::MAILBOX_DEFAULT) {
			return false;
		}

		return $this->loadDefault();
	}

	
	/**
	 * Load default Mailbox profile
	 * 
	 * @param type $data 
	 */
	public function loadDefault()
	{
		return parent::load($this->getDefaultId());
	}
	
	
	/**
	 * Get Mailbox default profile or J! profile if the default is not configured.
	 *
	 * @param string $name - id of a letter
	 *
	 * @return object - list of subscribers
	 * @since 1.0
	 */
	public function getDefaultId()
	{
		$options = JComponentHelper::getComponent('com_newsletter');
		$options = $options->params->toArray();

		return empty($options['general_mailbox_default']) ? 0 : (int) $options['general_mailbox_default'];
	}


	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 *
	 * @return	JTable	A database object
	 * @since	1.0.4
	 */
	public function getTable($type = 'mailboxprofile', $prefix = 'NewsletterTable')
	{
		return JTable::getInstance($type, $prefix);
	}

}
