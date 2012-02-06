<?php

/**
 * The newsletters list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
JLoader::import('helpers.statistics', JPATH_COMPONENT_ADMINISTRATOR, '');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

/**
 * Class of the newsletters list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewNewsletters extends MigurView
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

		//TODO: Need to move css/js to SetDocument

		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/newsletters.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/filterpanel.js');
		JHTML::script('media/com_newsletter/js/migur/js/search.js');
		JHTML::script('media/com_newsletter/js/migur/js/raphael-min.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.raphael.js');
		JHTML::script('media/com_newsletter/js/migur/js/g.pie.js');
		JHTML::script('media/com_newsletter/js/migur/js/raphael-migur-pie.js');
		JHTML::script(JURI::root() . "/administrator/components/com_newsletter/views/newsletters/newsletters.js");

		$this->setModel(
			JModel::getInstance('lists', 'NewsletterModel')
		);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		JHTML::_('behavior.modal');

		// Let's work with model 'newsletters' !
		$model = $this->getModel('newsletters');
		$items = $model->getItems();
		$pagination = $model->getPagination();
		$state = $model->getState();
		$listOrder = $model->getState('list.ordering');
		$listDirn = $model->getState('list.direction');
                
		$saveOrder = $listOrder == 'a.ordering';

		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('state', $state);
		$this->assignRef('listOrder', $listOrder);
		$this->assignRef('listDirn', $listDirn);
		$this->assignRef('saveOrder', $saveOrder);

		$this->setStatisticsData();

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
		JToolBarHelper::title(JText::_('COM_NEWSLETTER_NEWSLETTERS_TITLE'), 'article.png');

		$bar = JToolBar::getInstance('newsletters');
		$bar->appendButton('Link', 'default', 'COM_NEWSLETTER_SHOW_STATISTICS', 'index.php?option=com_newsletter&amp;view=statistic&amp;tmpl=component');
		
		if (AclHelper::actionIsAllowed('newsletter.add')) {
			$bar->appendButton('Standard', 'new', 'JTOOLBAR_NEW', 'newsletter.add', false);
			$bar->appendButton('Standard', 'copy', 'JTOOLBAR_SAVE_AS_COPY', 'newsletter.save2copy', false);
		}	
		$bar->appendButton('Standard', 'trash', 'JTOOLBAR_DELETE', 'newsletters.delete', false);

		// Load the submenu.
		NewsletterHelper::addSubmenu(JRequest::getVar('view'));
	}

	/**
	 * Gets the statistics data and set it to JS.
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setStatisticsData()
	{
		$data = StatisticsHelper::totalSent();
		JavascriptHelper::addObject('statTotalSent', $data);


		$data = StatisticsHelper::openedActionsCount();
		JavascriptHelper::addObject('statOpenedCount', $data);


		$data = StatisticsHelper::openedNewslettersCount();
		$res = array(
			'newsletters' => empty($data['newsletters']) ? 0 : $data['newsletters'],
			'subscribers' => empty($data['subscribers']) ? 0 : $data['subscribers'],
		);
		JavascriptHelper::addObject('statActiveSubscribersCount', $res);

		$now = date('Y-m-d H:i:s');
		$sevenDaysBefore = date('Y-m-d', strtotime('-7 Days', time())) . " 00:00:00";
		$thirtyDaysBefore = date('Y-m-d', strtotime('-30 Days', time())) . " 00:00:00";
		$ninetyDaysBefore = date('Y-m-d', strtotime('-90 Days', time())) . " 00:00:00";


		$this->totalSubs = array(
			StatisticsHelper::totalSubscribersCount($sevenDaysBefore, $now),
			StatisticsHelper::totalSubscribersCount($thirtyDaysBefore, $now),
			StatisticsHelper::totalSubscribersCount($ninetyDaysBefore, $now)
		);

		$this->newSubs = array(
			StatisticsHelper::newSubscribersCount($sevenDaysBefore, $now),
			StatisticsHelper::newSubscribersCount($thirtyDaysBefore, $now),
			StatisticsHelper::newSubscribersCount($ninetyDaysBefore, $now)
		);

		$this->lostSubs = array(
			StatisticsHelper::lostSubscribersCount($sevenDaysBefore, $now),
			StatisticsHelper::lostSubscribersCount($thirtyDaysBefore, $now),
			StatisticsHelper::lostSubscribersCount($ninetyDaysBefore, $now)
		);

		$this->activeSubs = array(
			StatisticsHelper::activeSubscribersCount($sevenDaysBefore, $now),
			StatisticsHelper::activeSubscribersCount($thirtyDaysBefore, $now),
			StatisticsHelper::activeSubscribersCount($ninetyDaysBefore, $now)
		);
	}

}
