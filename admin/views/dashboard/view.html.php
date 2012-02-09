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


		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/dashboard.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/raphael-min.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.raphael-min.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.line-min.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.raphael.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.line.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.pie.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.bar.js');
		JHTML::script('media/com_newsletter/js/migur/js/raphael-migur-line.js');


		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		EnvironmentHelper::showWarnings(array(
			'checkJoomla',
			'checkImap',
			'checkLogs'));
		
		$this->addToolbar();

		$stat = QueueHelper::getCount();
		$sent = 0;
		$toSend = 0;
		$total = 0;
		foreach ($stat as $row) {
			$sent += $row['sent'];
			$toSend += $row['to_send'];
			$total += $row['total'];
		}

		JavascriptHelper::addStringVar('emailsSent', $sent);
		JavascriptHelper::addStringVar('emailsToSend', $toSend);
		JavascriptHelper::addStringVar('emailsTotal', $total);
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

		parent::display($tpl);

		// Set the document
		$this->setDocument();
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
		$bar->appendButton('Link', 'alert', 'COM_NEWSLETTER_NOTIFICATIONS', 'index.php?option=com_newsletter&amp;view=logs');

		JToolBarHelper::custom('', 'progress', '', '', false);

		$bar = MigurToolBar::getInstance('newsletters-toolbar');
		$bar->appendButton('Link', 'new', 'COM_NEWSLETTER_NEWSLETTER_CREATE', 'index.php?option=com_newsletter&amp;view=newsletter');
		$bar->appendButton('Popup', 'export', 'COM_NEWSLETTER_NEWSLETTER_SEND', 'index.php?option=com_newsletter&amp;view=sender&amp;tmpl=component', 920, 450, 0, 0);

		$bar = MigurToolBar::getInstance('subscribers-toolbar');
		$bar->appendButton('Popup', 'new', 'COM_NEWSLETTER_SUBSCRIBER_CREATE', 'index.php?option=com_newsletter&amp;view=subscriber&amp;tmpl=component', 400, 220, 0, 0);
		$bar->appendButton('Popup', 'new', 'COM_NEWSLETTER_LIST_CREATE', 'index.php?option=com_newsletter&amp;view=list&amp;tmpl=component', 1000, 600, 0, 0);

		$bar = MigurToolBar::getInstance('config-toolbar');
		$bar->appendButton('Popup', 'export', 'COM_NEWSLETTER_EXTENSIONS_INSTALL', 'index.php?option=com_newsletter&amp;view=extension&amp;layout=install&amp;tmpl=component', 350, 150, 0, 0);
		$bar->appendButton('Link', 'options', 'COM_NEWSLETTER_CONFIGURATION', 'index.php?option=com_newsletter&amp;view=configuration');

		$bar = MigurToolBar::getInstance('help-toolbar');
		$bar->appendButton('Popup', 'publish', 'COM_NEWSLETTER_ABOUT', 'http://migur.com/products/newsletter', 800, 600, 0, 0);
		$bar->appendButton('MigurHelp', 'help', 'COM_NEWSLETTER_HELP', 'http://migur.com/support/documentation/newsletter');

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
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/subscriber/submitbutton.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/dashboard/dashboard.js");
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
