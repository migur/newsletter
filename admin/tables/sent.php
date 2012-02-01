<?php

/**
 * The sent table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of sent table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableSent extends JTable
{

	const BOUNCED_NO =        'NO';
	const BOUNCED_SOFT =      'SOFT';
	const BOUNCED_HARD =      'HARD';
	const BOUNCED_TECHNICAL = 'TECHNICAL';

	/**
	 * The constructor of a class.
	 *
	 * @param	object	$config		An object of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(&$_db)
	{
		parent::__construct(
				'#__newsletter_sent',
				'sent_id',
				$_db
		);
	}

	/**
	 * Get all allowed action names (used via JText).
	 *
	 * @return array of action names
	 */
	public function getBounces()
	{
		$oClass = new ReflectionClass(get_class($this));
		$consts = $oClass->getConstants();
		$this->actions = array();
		foreach ($consts as $name => $item) {
			if (substr($name, 0, 7) == 'BOUNCED') {
				$this->actions[$name] = $item;
			}
		}
		return $this->actions;
	}
	
	public function deleteAll()
	{

		$query = $this->_db->getQuery(true);
		$query->delete();
		$query->from($this->_tbl);
		$this->_db->setQuery($query);

		if (!$this->_db->query()) {
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
			$this->setError($e);
			return false;
		}

		return true;
	}

	
	public function setBounced($sid, $nid, $bounceType)
	{
		$bounceType = 'BOUNCED_'. strtoupper($bounceType);
		$bounces = $this->getBounces();
		
		if (!in_array($bounceType, array_keys($bounces))) {
			return false;
		}

		$this->load(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid
		));

		if (empty($this->sent_id)){
			return false;
		}

		return $this->save(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid,
			'bounced' => $bounces[$bounceType],
			'recieved_date' => date('Y-m-d H:i:s')
		));
	}


	/**
	 * Pre-store. Convert 'extra' to json
	 * 
	 * @param type $updateNulls 
	 */
	public function store($updateNulls = false)
	{
		if (!empty($this->extra) && !is_string($this->extra)) {
			$this->extra = json_encode($this->extra);
		}
		
		return parent::store($updateNulls);
	}


	/**
	 * Post-load. Convert 'extra' to array
	 * 
	 * @param type $updateNulls 
	 */
	public function load($keys = null, $reset = true)
	{
		if (!parent::load($keys, $reset)) {
			return false;
		}
		
		if (!empty($this->extra) && !is_array($this->extra)) {
			$this->extra = (array)json_decode($this->extra);
		}
		
		return true;
	}
	
	
}

