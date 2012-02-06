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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
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
		$isNew = (!JRequest::getInt('list_id', false) );
		
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

		switch(JRequest::getString('subtask', '')) {
			case 'import':
				$this->subtask = 1;
				break;
			case 'exclude':
				$this->subtask = 2;
				break;
			default:
				$this->subtask = 0;
		}
		JavaScriptHelper::addStringVar('subtask', $this->subtask);


		$script = $this->get('Script');
		$this->script = $script;

		$this->listForm = $this->get('Form', 'list');

		$this->setModel(
			JModel::getInstance('subscribers', 'NewsletterModel')
		);

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
		$model = JModel::getInstance('lists', 'NewsletterModel');
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

		if ($config->get('enable_flash', 1)) {

			JHTML::stylesheet('media/com_newsletter/css/uploaders.css');
			JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/list/uploaders.js");


			$fileTypes = $config->get('image_extensions', 'bmp,gif,jpg,png,jpeg');
			$types = explode(',', $fileTypes);
			$displayTypes = '';  // this is what the user sees
			$filterTypes = '';  // this is what controls the logic
			$firstType = true;

			foreach ($types AS $type) {
				if (!$firstType) {
					$displayTypes .= ', ';
					$filterTypes .= '; ';
				} else {
					$firstType = false;
				}

				$displayTypes .= '*.' . $type;
				$filterTypes .= '*.' . $type;
			}

			$typeString = '{ \'' . JText::_('COM_MEDIA_FILES', 'true') . ' (' . $displayTypes . ')\': \'' . $filterTypes . '\' }';
		}

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		jimport('joomla.client.helper');
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->assignRef('session', JFactory::getSession());
		$this->assignRef('config', $config);
		$this->assignRef('state', $this->get('state'));
		$this->assignRef('folderList', $this->get('folderList'));
		$this->assign('require_ftp', $ftp);

		$this->setStatisticsData();

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
		$isNew = !JRequest::getInt('list_id', false);
		
		$bar = JToolBar::getInstance('multitab-toolbar');
		if (
			( $isNew && AclHelper::actionIsAllowed('list.add')) || 
			(!$isNew && AclHelper::actionIsAllowed('list.edit'))
		) {
			$bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'list.apply', false);
			$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'list.save', false);
		}	
		$bar->appendButton('Link', 'cancel', 'JTOOLBAR_CLOSE', 'index.php?option=com_newsletter&view=close&tmpl=component', false);

		$bar = MigurToolBar::getInstance('import-toolbar');
		$bar->appendButton('Link', 'export', 'COM_NEWSLETTER_IMPORT_FROM_FILE', '#');
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

		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/admin.css');
		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/list.css');
		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/uploaders.css');

		$document->addScript(JURI::root() . $this->script);
		
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/core.js');
		$document->addScript('/joomla/media/system/js/tabs.js');

		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/raphael-min.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.raphael-min.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.line-min.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.pie-min.js');
		
		$document->addScript(JURI::root() . "media/system/js/progressbar.js");
		$document->addScript(JURI::root() . "media/system/js/swf.js");
		$document->addScript(JURI::root() . 'media/system/js/uploader.js');
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/list/list.js");
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/list/submitbutton.js");
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/models/forms/list.js", true);

		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/raphael-min.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.raphael.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.line.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.pie.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/g.bar.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/raphael-migur-line.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/raphael-migur-pie.js');
		$document->addScript(JURI::root() . 'media/com_newsletter/js/migur/js/message.js');

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
