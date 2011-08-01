<?php

/**
 * The controller for sender view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerSender extends JControllerForm
{

	/**
	 * Class Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 * 
	 * @return	void
	 * @since	1.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Handles the sending of the letter to a lists. 
	 * Adds the items to "_queue" table.
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function addToQueue()
	{

		$newsletterId = JRequest::getInt('newsletter_id');
		$lists = JRequest::getVar('lists');


		$subscribers = array();
		$table = JTable::getInstance('queue', 'NewsletterTable');

		if (empty($newsletterId) || empty($lists)) {
			$this->setError('Required data is absent');
			echo json_encode(array('state' => '0', 'error' => 'Required data is absent'));
			return false;
		}

		foreach ($lists as $list) {

			$dbo = JFactory::getDbo();
			$query = $dbo->getQuery(true);
			$query->select('distinct s.subscriber_id')
				->from('#__newsletter_sub_list AS sl')
				->join('', '#__newsletter_subscribers AS s ON s.subscriber_id = sl.subscriber_id')
				->where('list_id=' . (int) $list);

			//echo nl2br(str_replace('#__','jos_',$query));
			$subs = $dbo->setQuery($query)->loadAssocList();

			//var_dump($subs); die();
			if (!empty($subs)) {
				foreach ($subs as $item) {

					// do not add the new row if it exists...

					$table->set('queue_id', null);

					$res = $table->load(array(
							'newsletter_id' => $newsletterId,
							'subscriber_id' => $item['subscriber_id']
						));

					if (!$res) {
						$table->save(array(
							'newsletter_id' => $newsletterId,
							'subscriber_id' => $item['subscriber_id'],
							'created' => date('Y-m-d H:i:s'),
							'state' => 1
						));
					}
				}
			}
		}

		echo json_encode(array('state' => '1', 'error' => 'Ok'));
		return true;
	}

}

