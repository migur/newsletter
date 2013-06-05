<?php

/**
 * The list view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// import view library
JLoader::import('helpers.statistics', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.support', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('plugins.manager', JPATH_COMPONENT_ADMINISTRATOR, '');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
jimport('migur.library.toolbar');

/**
 * Class of the list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewList extends MigurView
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
		$app = JFactory::getApplication();
		
		$isNew = (!JRequest::getInt('list_id', false) );
		
		$model =            MigurModel::getInstance('lists', 'NewsletterModel');
		$listModel =        MigurModel::getInstance('List', 'NewsletterModel');
		$subscribersModel = MigurModel::getInstance('subscribers', 'NewsletterModel');
		
		$this->setModel($subscribersModel);
		
		$this->assign('list', $listModel->getItem());
		
		if (
			( $isNew && !AclHelper::actionIsAllowed('list.add')) ||
			(!$isNew && !AclHelper::actionIsAllowed('list.edit'))
		) {
			$msg = $isNew? 'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED' : 'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED';
			JFactory::getApplication()->redirect(
				JRoute::_('index.php?option=com_newsletter&view=subscribers', false),
				JText::_($msg), 
				'error');
			return;
		}	
		
		
		//TODO: Bulk-code. Need to refactor

		$listId = JRequest::getInt('list_id', 0);
		
		$activeTab = JRequest::getInt('activetab', 0);
		
		$subtask = 0;
		switch(JRequest::getString('subtask', '')) {
			case 'import':
				$subtask = 1;
				$activeTab = 1;
				break;
			case 'exclude':
				$subtask = 2;
				$activeTab = 2;
				break;
			default:
				$subtask = 0;
		}
		
		$this->assign('activeTab', $activeTab);
		
		JavaScriptHelper::addStringVar('subtask', $subtask);

		$this->listForm = $this->get('Form', 'list');

		$sess = JFactory::getSession();
		$data = $sess->get('list.' . $listId . '.file.uploaded');
		if ($data) {
			JavaScriptHelper::addObject('uploadData', $data);
		}

		$modelSubs = new NewsletterModelSubscribers();
		$modelSubs->setState('list.limit', 10);
		
		if (!empty($listId)) {
			$this->subs = $modelSubs->getSubscribersByList(array(
				'list_id' => JRequest::getInt('list_id')
			));

			$items = $modelSubs->getUnsubscribedList(array(
				'list_id' => JRequest::getInt('list_id')
			));
		} else {
			$items = array();
			$this->subs = array();
		}
		
		$ss = (object) array(
				'items' => $items,
				'state' => $modelSubs->getState(),
				'listOrder' => $modelSubs->getState('list.unsubscribed.ordering'),
				'listDirn' => $modelSubs->getState('list.unsubscribed.direction')
		);
		$this->assignRef('subscribers', $ss);


		// get data for "excluded"
		// get only active lists
		$model->setState('filter.fields', array(
			'a.state="1"',
			'a.list_id<>' . JRequest::getInt('list_id')
		));

		$this->lists = (object) array(
				'items' => $model->getItems(),
				'state' => $model->getState(),
				'listOrder' => $model->getState('list.ordering'),
				'listDirn' => $model->getState('list.direction')
		);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		$this->addToolbar();

		$config = JComponentHelper::getParams('com_media');
		$app = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$append = '';

//		if ($config->get('enable_flash', 1)) {
//
//			NewsletterHelperView::addStyleSheet('media/com_newsletter/css/uploaders.css');
//			NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/uploaders.js");
//
//
//			$fileTypes = $config->get('image_extensions', 'bmp,gif,jpg,png,jpeg');
//			$types = explode(',', $fileTypes);
//			$displayTypes = '';  // this is what the user sees
//			$filterTypes = '';  // this is what controls the logic
//			$firstType = true;
//
//			foreach ($types AS $type) {
//				if (!$firstType) {
//					$displayTypes .= ', ';
//					$filterTypes .= '; ';
//				} else {
//					$firstType = false;
//				}
//
//				$displayTypes .= '*.' . $type;
//				$filterTypes .= '*.' . $type;
//			}
//
//			$typeString = '{ \'' . JText::_('COM_MEDIA_FILES', 'true') . ' (' . $displayTypes . ')\': \'' . $filterTypes . '\' }';
//		}

		
//		if(!empty($listId)) {
//			$this->assign('events', $listModel->getEventsCollection($listId));
//		}	
		
		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->assignRef('config', $config);
		$this->assign('session', JFactory::getSession());
		$this->assign('state', $this->get('state'));
		$this->assign('folderList', $this->get('folderList'));
		$this->assign('require_ftp', $ftp);

		$this->setStatisticsData();

        // Handle import plugins
	
        $plgManager = NewsletterPluginManager::factory('import');
		
        $res = $plgManager->trigger(array(
            'group' => 'list.import',
            'event' => 'onMigurImportShowIcon'
        ));
        
        $this->assignRef('importPlugins', $res);
        
		// Set the document
		$this->setDocument();
		
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
		$lid = JRequest::getInt('list_id', false);
		$isNew = !$lid;
		JToolbarHelper::title($isNew? JText::_('COM_NEWSLETTER_TITLE_CREATE_LIST') : JText::sprintf('COM_NEWSLETTER_TITLE_EDIT_LIST', '"'.$this->list->name.'"'));
		
		$bar = JToolBar::getInstance();
		if (
			( $isNew && AclHelper::actionIsAllowed('list.add')) || 
			(!$isNew && AclHelper::actionIsAllowed('list.edit'))
		) {
			$bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'list.apply', false);
			$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'list.save', false);
		}	
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CLOSE', 'list.cancel', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = !JRequest::getInt('list_id', false);
		$this->isNew = $isNew;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_LIST_CREATING') : JText::_('COM_NEWSLETTER_LIST_EDITING'));

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/list.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/modal.js');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.raphael-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.line-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.pie-min.js');
		
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/importer.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/excluder.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/list.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/submitbutton.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/plugins.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/views/list/eventwidget.js");
		NewsletterHelperView::addScript("administrator/components/com_newsletter/models/forms/list.js", true);

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/storage.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-min.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.raphael.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.line.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.pie.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/g.bar.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-migur-line.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/raphael-migur-pie.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/iterativeajax.js');
		// Add JS and create namespace for data
		NewsletterHelperView::addScript("media/com_newsletter/js/migur/js/support.js");

		$document->addScriptDeclaration('var urlRoot = "' . JURI::root(true) . '";');
		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}

	/**
	 * Calculate the statistics data and add it to the JS
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setStatisticsData()
	{
		$data = StatisticsHelper::totalSent();
		$res = array(
			'no' => empty($data['no']) ? 0 : $data['no'],
			'soft' => empty($data['soft']) ? 0 : $data['soft'],
			'hard' => empty($data['hard']) ? 0 : $data['hard'],
			'total' => empty($data['total']) ? 0 : $data['total']
		);
		JavascriptHelper::addObject('statTotalSent', $res);


		$data = StatisticsHelper::openedActionsCount();
		$res = array(
			'other' => empty($data['other']) ? 0 : $data['other'],
			'opened' => empty($data['opened']) ? 0 : $data['opened'],
			'total' => empty($data['total']) ? 0 : $data['total']
		);
		JavascriptHelper::addObject('statOpenedCount', $res);


		$data = StatisticsHelper::openedNewslettersCount();
		$res = array(
			'newsletters' => empty($data['newsletters']) ? 0 : $data['newsletters'],
			'subscribers' => empty($data['subscribers']) ? 0 : $data['subscribers'],
		);
		JavascriptHelper::addObject('statActiveSubscribersCount', $res);

		$theHour = 3600;
		$theDay = $theHour * 24;
		$days = 30;
		$previousDay    = date('Y-m-d 00:00:00', time() - $theDay);
		$fiewDaysBefore = date('Y-m-d 00:00:00', time() - $theDay * $days);

		JavascriptHelper::addObject('newSubsPerDay',
			StatisticsHelper::newSubscribersPerDay(
				$fiewDaysBefore,
				$previousDay
			)
		);
	}
}
