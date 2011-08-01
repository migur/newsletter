<?php
/**
 * The controller for lists view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NewsletterControllerLists extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_LISTS';

	/**
	 * Proxy for getModel.
	 *
	 * @since	1.0
	 */
	public function getModel($name = 'List', $prefix = 'NewsletterModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}


	/**
	 * Change the standard behavior after publishing
	 *
	 * @since	1.0
	 */
	public function publish()
	{
            parent::publish();
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=subscribers'));
        }

	/**
	 * Change the standard behavior after deleting
	 *
	 * @since	1.0
	 */
	public function delete()
	{
            parent::delete();
            $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view=subscribers'));
        }
}