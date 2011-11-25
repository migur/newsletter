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
		JHTML::stylesheet('media/com_newsletter/css/admin.css');
		JHTML::stylesheet('media/com_newsletter/css/extension.css');
		JHTML::script('media/com_newsletter/js/migur/js/core.js');
		JHTML::script(JURI::root() . "/administrator/components/com_newsletter/views/extension/submitbutton.js");

		$type        = JRequest::getString('type', '');
		$native      = JRequest::getInt('native', null);
		$extensionId = JRequest::getInt('extension_id', 0);

		if ($type == 'plugin') {
			$exts = MigurPluginHelper::getSupported(array(
				'extension_id' => $extensionId,
				'native'       => $native
			));
		} else {
			$exts = MigurModuleHelper::getSupported(array(
				'extension_id' => $extensionId,
				'native'       => $native
			));
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_content', JPATH_ADMINISTRATOR, null, false, false);

		$ext = $exts[0];
		$this->info = $ext->xml;
		if (JRequest::getString('layout') == 'edit') {
			$model = $this->getModel();
			$this->form = $model->getForm(array(
				'module' => $ext->extension,
				'native' => $ext->native,
				'type'   => $ext->type
			));
		}

		parent::display($tpl);
	}

}
