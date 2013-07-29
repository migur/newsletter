<?php

/**
 * The newsletter view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('migur.library.toolbar');
jimport('joomla.utilities.simplexml');
jimport('joomla.html.html.sliders');
JLoader::import('helpers.mail',   JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.plugin', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.download', JPATH_COMPONENT_ADMINISTRATOR, '');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.modal');

/**
 * Class of the newsletter view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewNewsletter extends MigurView
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
		$nId = JRequest::getInt('newsletter_id');

		$isNew = empty($nId);
		
		if (
			( $isNew && !AclHelper::actionIsAllowed('newsletter.add')) ||
			(!$isNew && !AclHelper::actionIsAllowed('newsletter.edit'))
		) {
			$msg = $isNew? 'JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED' : 'JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED';
			JFactory::getApplication()->redirect(
				JRoute::_('index.php?option=com_newsletter&view=newsletters', false),
				JText::_($msg), 
				'error');
			return;
		}	
		
		// Get main form and data for newsletter
		$newsletterModel = MigurModel::getInstance('Newsletter', 'NewsletterModel');
		$newsletter = $newsletterModel->getItem($nId);
		
		$this->assignRef('newsletter', $newsletter);
		$this->assign('form', $this->get('Form', 'newsletter'));
		
		$isUpdateAllowed = $newsletterModel->isUpdateAllowed($newsletter);
		
		$this->assign('isUpdateAllowed', $isUpdateAllowed);
		
		JavascriptHelper::addStringVar('isUpdateAllowed', (int) $isUpdateAllowed);
		
		JavascriptHelper::addObject(
				'comParams',
				JComponentHelper::getParams('com_newsletter')->toArray() //array('autosaver' => array('on' => true))
		);
		
		$smtpModel = MigurModel::getInstance('Smtpprofile', 'NewsletterModelEntity'); 
		
		// Let's add J! profile
		$smtpp = $smtpModel->loadJoomla();
		JavascriptHelper::addObject(
				'joomlaDe',
				JComponentHelper::getParams('com_newsletter')->toArray() //array('autosaver' => array('on' => true))
		);
		
		// get the SmtpProfiles data
		$smtpprofilesManager = MigurModel::getInstance('smtpprofiles', 'NewsletterModel');
		$this->assign('smtpprofiles', $smtpprofilesManager->getAllItems('withDefault'));

		// get all the Extensions
		$this->modules = MigurModuleHelper::getSupported(array('withoutInfo'=>true));
		$this->plugins = MigurPluginHelper::getSupported(array('withoutInfo'=>true), 'newsletter.html');

		// get the Extensions used in this newsletter
		$model = MigurModel::getInstance('newsletterext', 'NewsletterModel');
		$this->usedExts = $model->getExtensionsBy($nId);
		
		// Get a list of all templates
		$templateModel = MigurModel::getInstance('templates', 'NewsletterModel');
		$this->setModel($templateModel);
		$model = $this->getModel('templates');
		$templs = $model->getItems();
		$path = JPATH_COMPONENT . '/extensions/templates/';
		$filenames = JFolder::files($path, '^.*\.xml$');
		if ($filenames === false) {
			JError::raiseError(500, implode("\n", array("Path $path not found")));
		}

		$this->templates = (object) array(
				'items' => array(),
				'path' => $path
		);

		$this->htmlTemplateId = null;

		foreach ($templs as $item) {

			$xml = simplexml_load_file($path . $item->template, 'SimpleXMLElement', LIBXML_NOCDATA);
			$str = trim((string)$xml->template);
			$str = preg_replace('/<style.*>.*<\/style>/s', '', $str);
			$str = str_replace('<position', '<div class="drop container-draggables"', $str);

			$id = strtolower(str_replace('.', '-', $item->template) . '-' . $item->t_style_id);
			$item->id = $id;
			$item->filename = $item->template;
			$item->template = $str;

			$this->templates->items[] = $item;

			if ($this->newsletter->t_style_id == $item->t_style_id) {
				$this->htmlTemplateId = $id;
				$this->t_style_id = $item->t_style_id;
			}

			unset($xml);
		}

		//attachments
		$this->attItems = array();

		$this->dynamicData = array(
			'Name' => '[username]',
			'Email' => '[useremail]',
			'Site name'      =>   '[sitename]',
			'Subscription key' => '[subscription key]',
			'Unsubscription link' => '[unsubscription link]',
			'Confirmation link'   => '[confirmation link]'
		);



		$this->attItemslistDirn = "a.filename";
		$this->attItemslistOrder = "asc";

		// getting of an xml from
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		$this->downloads = (array)DownloadHelper::getByNewsletterId($nId);

		JavascriptHelper::addObject('dataStorage',

            (object)array(
                'htmlTemplate' => (object)array(
                    'template' => (object)array(
                        'id' => $this->htmlTemplateId),
                    'extensions' => (array)$this->usedExts),
                'templates' => (array)$this->templates->items,
                'modules' => (array)$this->modules,
                'plugins' => (array)$this->plugins,
				'newsletter' => NewsletterHelper::get($nId)
            )
			
        );

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
		$isNew = (!JRequest::getInt('newsletter_id', false) );
		JToolBarHelper::title($isNew? 
			JText::_('COM_NEWSLETTER_NEWSLETTERS_ADD_TITLE') : 
			($this->isUpdateAllowed? 
				JText::sprintf('COM_NEWSLETTER_NEWSLETTERS_EDIT_TITLE', $this->newsletter->name) :
				JText::sprintf('COM_NEWSLETTER_NEWSLETTERS_REVIEW_TITLE', $this->newsletter->name)
			), 
		'article.png');

		$bar = JToolBar::getInstance('toolbar');
		
		try {
			$status = NewsletterHelperNewsletter::getLicenseStatus();
		
			// We show tutorials only for users with valid license
			if ($status->isValid) {
				
				$helpLink = 'http://migur.com/support/documentation/migur-newsletter/newsletters?version=' . NewsletterHelper::getManifest()->version;
				$bar->appendButton(
					'Custom', 
					'<a class="btn btn-small" href="'.$helpLink.'" target="_blank">'.
					'<span class="icon-32-default"></span>'.JText::_('COM_NEWSLETTER_TUTORIAL').'</a>'
				);
				$bar->appendButton('Separator', null, '25');
			}	
		
		} catch(Exception $e) {
			NewsletterHelperLog::addError($e->getMessage());
		}

		

		if ($this->isUpdateAllowed && (
				( $isNew && AclHelper::actionIsAllowed('newsletter.add' )) ||
				(!$isNew && AclHelper::actionIsAllowed('newsletter.edit')) 
			)
		) {
			$bar->appendButton('Link', 'autosaver', '', '#', false);
			$bar->appendButton('Separator', null, '25');
			$bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'newsletter.apply', false);
			$bar->appendButton('Standard', 'save',  'JTOOLBAR_SAVE', 'newsletter.save', false);

//			$bar->appendButton('Separator', null, '50');
//			$bar->appendButton('Custom', '<button class="btn btn-small" id="autosaver-switch"><span id="autosaver-icon"></span><span id="content-state"></span></button>', 'autosaver', '', false);
//			$bar->appendButton('Separator', null, '25');
//			$bar->appendButton('Custom', '<span></span>', 'docstate', '', false);
		}	
		
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', 'newsletter.cancel', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::getInt('newsletter_id', false) );
		JavascriptHelper::addStringVar('isNew', (int)$isNew);
		$document = JFactory::getDocument();
		$document->setTitle($isNew? JText::_('COM_NEWSLETTER_NEWSLETTER_CREATING') : JText::sprintf('COM_NEWSLETTER_NEWSLETTERS_EDIT_TITLE', $this->newsletter->name));
		
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/newsletter.css');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
//		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/modal.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/ajax.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/widgets.js');
		
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/moodialog/MooDialog.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/moodialog/MooDialog.Request.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/moodialog/MooDialog.IFrame.js');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/js/migur/js/moodialog/css/MooDialog.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/autocompleter/Observer.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/autocompleter/Autocompleter.js');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/autocompleter/Autocompleter.Local.js');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/js/migur/js/autocompleter/css/Autocompleter.css');

		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/guide.js');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/guide.css');
		
//		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/html.js');
//		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/plain.js');
//		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/preview.js');
//		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/autosaver.js');
//		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/guide.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/newsletter.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/downloads.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/submitbutton.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/newsletter/sidebar.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/models/forms/newsletter.js');
	}

}
