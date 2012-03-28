<?php

/**
 * The history table file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Class of history table. Implement the functionality for it.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterTableHistory extends JTable
{
	/*
	 * The meaning of values for "action" table field
	 */
	const ACTION_ADDED = '1';
	const ACTION_BOUNCED = '2';
	const ACTION_CLICKED = '3';
	const ACTION_OPENED = '4';
	const ACTION_REMOVED = '5';
	const ACTION_SENT = '6';
	const ACTION_SIGNEDUP = '7';
	const ACTION_UNSUBSCRIBED = '8';

	/**
	 * The constructor of a class.
	 *
	 * @param	object	$config		An object of configuration settings.
	 *
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$_db)
	{
		parent::__construct(
				'#__newsletter_sub_history',
				'history_id',
				$_db
		);
	}

	/**
	 * Get all allowed action names (used via JText).
	 *
	 * @return array of action names
	 */
	public function getActions()
	{
		$oClass = new ReflectionClass(get_class($this));
		$consts = $oClass->getConstants();
		$this->actions = array();
		foreach ($consts as $name => $item) {
			if (substr($name, 0, 6) == 'ACTION') {
				$this->actions[$name] = $item;
			}
		}
		return $this->actions;
	}

	/* Get all allowed action names (used via JText).
	 *
	 * @return array of action names
	 */
	public function getActionCode($act)
	{
		$actions = $this->getActions();
		$action = 'ACTION_' . strtoupper($act);
		if (array_key_exists($action, $actions)) {
			return $actions[$action];
		}

		return false;
	}

	/**
	 * Creates the SQL statement for mapping the raw values
	 * on the JText-used names into the SQL result.
	 *
	 * @param  string $fieldName the name for which mapping is created
	 * @param  string $prefix    the prefix
	 *
	 * @return string the result string
	 * @since  1.0
	 */
	public function getMappingFor($fieldName, $prefix = '')
	{
		$actions = $this->getActions();
		$res = " (CASE {$prefix}{$fieldName}";
		foreach ($actions as $name => $item) {
			$res .= " WHEN '{$item}' THEN '{$name}'";
		}
		$res .= " ELSE '{$item}' END) as {$fieldName}";
		return $res;
	}
	
	
	public function setBounced($sid, $nid) 
	{
		if (empty($sid)) {
			return false;
		}

		$this->load(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid,
			'action' => NewsletterTableHistory::ACTION_BOUNCED
		));

		return $this->save(array(
			'subscriber_id' => $sid,
			'newsletter_id' => $nid,
			'action' => NewsletterTableHistory::ACTION_BOUNCED,
			'date' => date('Y-m-d H:i:s')
		));
	}

    /**
     * Prestore data modifications
     * 
     * @param type $updateNulls 
     */
    public function store($updateNulls = false)
	{
        // Latest modification before ALL ways of data storing 
        
        // Need to set it to NULL if it is empty (need for FKs)
        if (isset($this->newsletter_id) && empty($this->newsletter_id)) {
            $src->newsletter_id = null;
        }

        if (isset($this->subscriber_id) && empty($this->subscriber_id)) {
            $src->subscriber_id = null;
        }

        if (isset($this->list_id) && empty($this->list_id)) {
            $src->list_id = null;
        }
        
        return parent::store($updateNulls);
    }
}
