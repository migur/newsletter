<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * SMTPprofiles Field class for the Joomla Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldEncodings extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'encodings';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of available SMTP profiles).
	 * @since	1.0
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array(
			array('value' => 'utf-8',       'text' => JText::_('COM_NEWSLETTER_ENCODING_UTF8')),
			array('value' => 'us-ascii', 'text' => JText::_('COM_NEWSLETTER_ENCODING_USASCII')),
			array('value' => 'iso-8859-1', 'text' => JText::_('COM_NEWSLETTER_ENCODING_ISO88591')),
			array('value' => 'windows-1251', 'text' => JText::_('COM_NEWSLETTER_ENCODING_WINDOWS1251')),
			array('value' => 'koi8-r', 'text' => JText::_('COM_NEWSLETTER_ENCODING_KOI8R')),
			array('value' => 'gb2312', 'text' => JText::_('COM_NEWSLETTER_ENCODING_GB2312')),
			array('value' => 'big5', 'text' => JText::_('COM_NEWSLETTER_ENCODING_BIG5')),
			array('value' => 'iso-2022-jp', 'text' => JText::_('COM_NEWSLETTER_ENCODING_ISO2022JP')),
			array('value' => 'iso-2022-kr', 'text' => JText::_('COM_NEWSLETTER_ENCODING_ISO2022KR')),
		);
		
		return $options;
	}

}
