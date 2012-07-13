<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_newsletter
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_newsletter
 */
class NewsletterControllerInstall extends JController
{
	/**
	 * Install an extension.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function install()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('install');
		if ($model->install()) {
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
			// TODO: Reset the users acl here as well to kill off any missing bits
		}

		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_newsletter.redirect_url');
		if(empty($redirect_url)) {
			$redirect_url = JRoute::_('index.php?option=com_newsletter&view=install', false);
		} else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_newsletter.redirect_url', '');
			$app->setUserState('com_newsletter.message', '');
			$app->setUserState('com_newsletter.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
	
	/**
	 * Install an extension.
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$eid	= JRequest::getVar('cid', array(), '', 'array');
		$model = $this->getModel('install');
		$model->remove($eid);

		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_newsletter.redirect_url');
		if(empty($redirect_url)) {
			$redirect_url = JRoute::_('index.php?option=com_newsletter&view=install', false);
		} else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_newsletter.redirect_url', '');
			$app->setUserState('com_newsletter.message', '');
			$app->setUserState('com_newsletter.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
	
	
	/**
	 * Enable/Disable an extension (if supported).
	 *
	 * @since	1.6
	 */
	public function restore()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$model = $this->getModel('install');
		$model->restore();

		$this->setRedirect(JRoute::_('index.php?option=com_newsletter&view=install', false));
	}
}
