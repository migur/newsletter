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
		$nid = JRequest::getString('nid', '');

		$user = JFactory::getUser();
		
		$subscriber = JTable::getInstance('Subscriber', 'NewsletterTable');

		if (!empty($uid)) {
			$subscriber->load(array('subscription_key' => $uid));
			
		} elseif(!empty($user->id)) {	
			$subscriber->load(array('user_id' => $user->id));
		}
		
		$lists = SubscriberHelper::getLists($subscriber->subscription_key);

		$this->assignRef('user', $user);
		$this->assignRef('subscriber', $subscriber);
		$this->assignRef('lists', $lists);
		$this->assignRef('uid',   $subscriber->subscription_key);
		$this->assignRef('nid',   $nid);

		$this->setDocument();

		parent::display();
	}

	function setDocument(){
		$document = JFactory::getDocument();
		$document->addStyleSheet('media/com_newsletter/css/unsubscribe.css');
	}
}
