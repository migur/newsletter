<?php
/**
 * @version		$Id:  $
 * @package		Components
 * @subpackage	com_newsletter
 * @copyright	Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * HTML Subscribe View class for the Newsletter component
 *
 * @package		Newsletter.Site
 * @subpackage	com_newsletter
 * @since 		1.0
 */
class NewsletterViewSubscribe extends JView
{
	function display($tpl = null)
	{
		$uid = JRequest::getString('uid', '');
		$subscriber = SubscriberHelper::getBySubkey($uid);

		if (empty($subscriber->subscriber_id)) {
			jexit('Subscriber not found');
		}
		
		$lists = SubscriberHelper::getLists($uid);

		$this->assignRef('subscriber', $subscriber);
		$this->assignRef('lists', $lists);
		$this->assignRef('uid',   $uid);

		$this->setDocument();

		parent::display();
	}

	function setDocument(){
		$document = JFactory::getDocument();
		$document->addStyleSheet('media/com_newsletter/css/unsubscribe.css');
	}
}
