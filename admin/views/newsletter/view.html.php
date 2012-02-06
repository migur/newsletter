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
		$isNew = (!JRequest::getInt('newsletter_id', false) );
		
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
		
		
		//TODO: Need to move css/js to SetDocument
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/newsletter.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script('media/com_newsletter/js/migur/js/ajax.js');
		JHTML::script('media/com_newsletter/js/migur/js/widgets.js');
		JHTML::script('media/com_newsletter/js/migur/js/moodialog/MooDialog.js');
		JHTML::script('media/com_newsletter/js/migur/js/moodialog/MooDialog.Request.js');
		JHTML::script('media/com_newsletter/js/migur/js/moodialog/MooDialog.IFrame.js');
		JHTML::stylesheet('media/com_newsletter/js/migur/js/moodialog/css/MooDialog.css');

		JHTML::script('media/com_newsletter/js/migur/js/autocompleter/Observer.js');
		JHTML::script('media/com_newsletter/js/migur/js/autocompleter/Autocompleter.js');
		JHTML::script('media/com_newsletter/js/migur/js/autocompleter/Autocompleter.Local.js');
		JHTML::stylesheet('media/com_newsletter/js/migur/js/autocompleter/css/Autocompleter.css');

		JHTML::script('media/com_newsletter/js/migur/js/guide.js');
		JHTML::stylesheet('media/com_newsletter/css/guide.css');

		//TODO: Bulk-code. Need to refactor.

		JavascriptHelper::addObject(
				'comParams',
				JComponentHelper::getParams('com_newsletter')->toArray() //array('autosaver' => array('on' => true))
		);

		$nId = JRequest::getInt('newsletter_id');

		$script = $this->get('Script');
		$this->script = $script;


		// Get main form and data for newsletter
		$this->form = $this->get('Form', 'newsletter');
		$this->newsletter = $this->get('Item');

		$smtpModel = JModel::getInstance('SMtpProfile', 'NewsletterModelEntity'); 
		
		// Let's add J! profile
		$smtpp = $smtpModel->loadJoomla();
		JavascriptHelper::addObject(
				'joomlaDe',
				JComponentHelper::getParams('com_newsletter')->toArray() //array('autosaver' => array('on' => true))
		);
		
		// get the SmtpProfiles data
		$smtpprofilesManager = JModel::getInstance('smtpprofiles', 'NewsletterModel');
		$this->assignRef('smtpprofiles', $smtpprofilesManager->getAllItems('withDefault'));

		// get all the Extensions
		$this->modules = MigurModuleHelper::getSupported(array('withoutInfo'=>true));
		$this->plugins = MigurPluginHelper::getSupported(array('withoutInfo'=>true), 'com_newsletter.newsletter');

		// get the Extensions used in this newsletter
		$model = JModel::getInstance('newsletterext', 'NewsletterModel');
		$this->usedExts = $model->getExtensionsBy($nId);
		
		// Get a list of all templates
		$this->setModel(
			JModel::getInstance('templates', 'NewsletterModel')
		);
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

			$xml = new JSimpleXML;
			$xml->loadFile($path . $item->template);
			$str = trim($xml->document->template[0]->_data);
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
			JText::_('COM_NEWSLETTER_NEWSLETTERS_EDIT_TITLE'), 
		'article.png');

		$bar = JToolBar::getInstance('toolbar');
		
		if (
			( $isNew && AclHelper::actionIsAllowed('newsletter.add' )) ||
			(!$isNew && AclHelper::actionIsAllowed('newsletter.edit')) 
		) {
			$bar->appendButton('Link', 'autosaver', '', '#', false);
			$bar->appendButton('Separator', null, '50');
			$bar->appendButton('Link', 'apply', 'JTOOLBAR_APPLY', '#', false);
			$bar->appendButton('Standard', 'save',  'JTOOLBAR_SAVE', 'newsletter.save', false);
		}
		
		$helpLink = 'http://migur.com/support/documentation/newsletter/' . NewsletterHelper::getManifest()->version . '/newsletters';
        $bar->appendButton('Popup', 'default', 'COM_NEWSLETTER_TUTORIAL', $helpLink, 1000, 600, 0, 0);
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
		$document->setTitle($isNew? JText::_('COM_NEWSLETTER_NEWSLETTER_CREATING') : JText::_('COM_NEWSLETTER_NEWSLETTER_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/newsletter/newsletter.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/newsletter/downloads.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/newsletter/submitbutton.js");
		JText::script('COM_NEWSLETTER_NEWSLETTER_ERROR_UNACCEPTABLE');
	}

}
