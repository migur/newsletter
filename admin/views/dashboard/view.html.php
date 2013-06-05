<?php

/**
 * The dashboard view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('migur.library.toolbar');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JLoader::import('helpers.queue', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.statistics', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.environment', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('simplepie.simplepie');

/**
 * Class of the dashboard view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewDashboard extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the template name
	 *
	 * @return void
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		EnvironmentHelper::showWarnings(array(
			'checkJoomla',
			'checkImap',
			'checkLogs'));

		$stat = MigurModel::getInstance('Queues', 'NewsletterModel')->getSummary();
		$sent = 0;
		$toSend = 0;
		$total = 0;
		foreach ($stat as $row) {
			$sent += $row['sent'];
			$total += $row['total'];
		}

		JavascriptHelper::addStringVar('mailsSent', $sent);
		JavascriptHelper::addStringVar('mailsToSend', $total - $sent);
		JavascriptHelper::addStringVar('mailsTotal', $total);
		JavascriptHelper::addStringVar('newslettersSent', count($stat));
		
		$cache = JFactory::getCache('com_newsletter');
		$this->news = $cache->call(
			array('RssfeedHelper', 'loadFeed'),
			new JObject(
					array('rssurl' => JRoute::_('http://migur.com/blog?format=feed&type=rss'))
			)
		);
		
		$this->info = NewsletterHelper::getCommonInfo();

		$this->setStatisticsData();

		$sess = JFactory::getSession();
		JavascriptHelper::addStringVar('sessname', $sess->getName());

		// Set the document
		$this->setDocument();

		$this->addToolbar();
		
		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_DASHBOARD_TITLE'), 'article.png');
		$bar = JToolBar::getInstance();
		
		$bar->appendButton('MigurBasic', 'COM_NEWSLETTER_PROCESS_QUEUE', array('id' => 'toolbar-queue', 'class' => 'btn btn-small btn-success'));
		$bar->appendButton('SendProgress');

		$bar->appendButton('Link', 'list', 'COM_NEWSLETTER_VIEW_QUEUE', 'index.php?option=com_newsletter&view=queues');
		$bar->appendButton('MigurBasic', 'COM_NEWSLETTER_PROCESS_BOUNCES', array('id' => 'toolbar-bounces', 'icon-class' => 'icon-refresh'));

		$bar->appendButton('Link', 'warning', 'COM_NEWSLETTER_NOTIFICATIONS', 'index.php?option=com_newsletter&amp;view=logs');
		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP_ABOUT_QUEUE', SupportHelper::getResourceUrl('mailing', 'general'));

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		//$document->setTitle('COM_NEWSLETTER_DASHBOARD_TITLE');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/dashboard.css');
		
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/modal.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.raphael-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.line-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.raphael.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.line.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.pie.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.bar.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-migur-line.js');

		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/subscriber/submitbutton.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/dashboard/dashboard.js");
		
		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}

	function setStatisticsData()
	{
		$theHour = 3600;
		$theDay = $theHour * 24;
		$days = 30;
		$previousDay = date('Y-m-d 00:00:00', strtotime("-1 day", time()));
		$fiewDaysBefore = date('Y-m-d 00:00:00', strtotime("-30 Days", time()));

		JavascriptHelper::addObject('opensPerDay',
				StatisticsHelper::openedNewslettersPerDay(
					$fiewDaysBefore,
					$previousDay,
					null
				)
		);
		JavascriptHelper::addObject('subsPerDay',
				StatisticsHelper::activeSubscribersPerDay(
					$fiewDaysBefore,
					$previousDay,
					null
				)
		);
	}

}
