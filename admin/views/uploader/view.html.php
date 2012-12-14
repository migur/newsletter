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

JHtml::_('behavior.framework', true);

/**
 * Class of the list view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewUploader extends MigurView
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
		$config = JComponentHelper::getParams('com_media');
		$this->assign('config', $config);

//		$session	= JFactory::getSession();
//		$state		= $this->get('state');
//		$this->session = $session;
//		
//		if ($config->get('enable_flash', 1)) {
//
//			JHTML::stylesheet('media/com_newsletter/css/uploaders.css');
//			JHTML::script(JURI::root() . "administrator/components/com_newsletter/views/list/uploaders.js");
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

		// Set the document
		$this->setDocument();
		
		parent::display($tpl);

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
		$document->addStylesheet(JURI::root() . 'media/com_newsletter/css/admin.css');
		$document->addScript(JURI::root() . "administrator/components/com_newsletter/views/uploader/uploader.js");
		
	}

}