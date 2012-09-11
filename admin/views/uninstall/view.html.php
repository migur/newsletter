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
class NewsletterViewUninstall extends MigurView
{
	public function display($tpl = null)
	{
		$cids = JRequest::getString('cid', '');
		$cids = (array) explode(',', $cids);
		
		if (empty($cids)) {
			$table = JTable::getInstance('Extension');
			$table->load(array('element' => 'com_newsletter'));
			$cids = (array) $table->extension_id;
		}	

		$this->assign('cids', $cids);
		
		return parent::display($tpl);
	}
}
