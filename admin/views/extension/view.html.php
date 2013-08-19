<?php

/**
 * The extension view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JLoader::import('helpers.module', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.plugin', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('migur.library.toolbar');
jimport('joomla.utilities.simplexml');
jimport('joomla.html.html.sliders');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.combobox');

/**
 * Class of the extension view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewExtension extends MigurView
{

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
		require_once COM_NEWSLETTER_PATH_ADMIN . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'modulelayout.php';

		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/admin.css');
		NewsletterHelperView::addStyleSheet('media/com_newsletter/css/extension.css');
		NewsletterHelperView::addScript('media/com_newsletter/js/migur/js/core.js');
		NewsletterHelperView::addScript('administrator/components/com_newsletter/views/extension/submitbutton.js');

		$type        = JRequest::getString('type', '');
		$native      = JRequest::getInt('native', null);
		$extensionId = JRequest::getInt('extension_id', 0);

		if ($type == 'plugin') {
			$exts = array(NewsletterHelperPlugin::getItem($extensionId, $native));
		} else {
			$exts = NewsletterHelperModule::getSupported(array(
				'extension_id' => $extensionId,
				'native'       => $native
			));
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_content', JPATH_ADMINISTRATOR, null, false, false);

		$ext = $exts[0];

		// Let's load lang file of a extension
		$basePath = $native?
			JPATH_BASE
			:
			COM_NEWSLETTER_PATH_ADMIN_EXTENSIONS .
			DIRECTORY_SEPARATOR . $type . 's' .
			DIRECTORY_SEPARATOR . $ext->extension;

		$lang->load($ext->extension, $basePath)
		|| $lang->load($ext->extension, $basePath, $lang->getDefault());


		$this->info = $ext->xml;
		if (JRequest::getString('layout') == 'edit') {
			$model = $this->getModel();
			$this->form = $model->getForm(array(
				'module'    => $ext->extension,
				'native'    => $ext->native,
				'type'      => $ext->type,
				'namespace' => !empty($ext->namespace)? $ext->namespace : '',

			));
			//var_dump($ext); die;
		}

		parent::display($tpl);
	}

}
