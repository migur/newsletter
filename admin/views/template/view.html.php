<?php

/**
 * The template view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import view library
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
jimport('joomla.application.component.view');
/**
 * Class of the template view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewTemplate extends MigurView
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
		JHTML::_('behavior.modal');

		$tStyleId = JRequest::getInt('t_style_id');
		
		$modelTemps = JModel::getInstance('Templates', 'NewsletterModel');
		$temps = (object) array(
				'items' => $modelTemps->getStandardTemplates(),
				'state' => $modelTemps->getState(),
				'listOrder' => $modelTemps->getState('list.ordering'),
				'listDirn' => $modelTemps->getState('list.direction')
		);
		$this->assignRef('templates', $temps);

		$script = $this->get('Script');
		$this->script = $script;

		$this->tplForm = $this->get('Form', 'template');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		if ($tStyleId > 0) {
			
			$model = $this->getModel();
			$template = $model->getTemplateBy($tStyleId);
			
			$this->assign('columns', $model->getColumnPlaceholders($template->content));
			$this->assign('tplInfo', (object)$template->information);

			$name = $this->tplForm->getValue('template_name');
			if (empty($name)) {
				$this->tplForm->setValue('template_name', null, $this->escape($this->tplInfo->name));
			}
		}
		
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
		$bar = JToolBar::getInstance('multitab-toolbar');
		$bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'template.save', false);
		$bar->appendButton('Standard', 'cancel', 'JTOOLBAR_CANCEL', '', false);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setDocument()
	{
		$isNew = (!JRequest::get('template_id', false) );
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_NEWSLETTER_TEMPLATE_CREATING') : JText::_('COM_NEWSLETTER_TEMPLATE_EDITING'));

		$document->addStylesheet(JURI::root() . '/media/com_newsletter/css/admin.css');
		$document->addStylesheet(JURI::root() . '/media/com_newsletter/css/templates.css');

		$document->addScript(JURI::root() . '/media/com_newsletter/js/migur/js/core.js');
		$document->addScript(JURI::root() . '/media/com_newsletter/js/migur/js/message.js');
		$document->addScript(JURI::root() . '/administrator/components/com_newsletter/views/template/template.js');
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/views/template/submitbutton.js");
		$document->addScript(JURI::root() . "/administrator/components/com_newsletter/models/forms/template.js");

		JText::script('COM_NEWSLETTER_SUBSCRIBER_ERROR_UNACCEPTABLE');
	}

}
