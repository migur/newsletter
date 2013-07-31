<?php

/**
 * The subscriber view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
JHtml::_('behavior.framework', true);
JHtml::_('behavior.tooltip');

/**
 * Class of the subscriber view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewSupport extends MigurView
{
	public $tmplPath = null;

	public $defaultName = 'general';

	public $defaultCategory = 'general';
	
	public function display($tpl = null)
	{
		$this->assign('version', JRequest::getString('version', null));
		$this->assign('category', JRequest::getString('category', null));
		$this->assign('name', JRequest::getString('name', null));
		$ltName = $this->getLayoutFileName($this->category, $this->name, $this->version);
		$this->assign('layout', $ltName);
		
		// Add JS and create namespace for data
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . "media/com_newsletter/css/support.css");

		
		return parent::display($tpl);
	}
	
	public function getLayoutFileName($category = null, $name = null, $version = null)
	{
		if (!$name) {
			$name = $this->defaultName;
		}

		if (!$category) {
			$category = $this->category;
		}
		
		if (empty($this->tmplPath)) {
			$this->tmplPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/support/tmpl';
		}
		
		if (!empty($version)) {
			$version = str_replace('.', '', trim($version, '-'));
		}
		
		$category = trim($category, '-');
		$name = trim($name, '-');
		
		$fullname = $category . '-' . $name;
		
		// Scan descending. Latest versions should be on the top
		$array = scandir($this->tmplPath, 1);

		foreach($array as $item) {
			
			// Dont look at dirs...
			if ($item == '.' || $item == '..' || !is_file($this->tmplPath . DIRECTORY_SEPARATOR. $item)) {
				continue;
			}
				
			// Get version and rest of the name
			@list($itemVer, $ItemFullname) = explode('_', $item);
		
			if (!is_numeric($itemVer)) {
				continue;
			}
			
			// Fill up version with the latest found if is not provided
			if (empty($version)) {
				$version = $itemVer;
			}
			
			$ItemFullname = substr($ItemFullname, 0, strpos($ItemFullname, '.php'));

			if (version_compare($version, $itemVer) >= 0 && $ItemFullname == $fullname) {
				return array('version' => $itemVer, 'fullname' => $ItemFullname);
			}
		}
	}
}
