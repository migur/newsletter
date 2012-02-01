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

JLoader::import('helpers.data', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * SMTPprofiles Field class for the Joomla Framework.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormFieldTimeinterval extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'timeinterval';

		
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup(span with mapped value).
	 * @since	1.0
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$class = (string)$this->element['class'];
		$name = (string)$this->name;
		$size = (string)$this->element['size'];
		$id = $this->id;
		
		// Parse value
		$value = $this->value;
		$vl = DataHelper::timeIntervalExplode($value);

		$valueCnt = 0;
		$valueType = 'day';
		
		if ($vl['weeks'] > 0) {
			$valueCnt = $vl['weeks'];
			$valueType = 'week';
		}
		
		if ($vl['days'] > 0) {
			$valueCnt = $vl['days'];
			$valueType = 'day';
		}
		

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}


		// Initialize JavaScript field attributes.
		JFactory::getDocument()->addScript(JUri::root().'administrator/components/com_newsletter/models/fields/timeinterval.js');

		
		// Create a regular list.
		$html  = '<div class="timeinterval" id="'.$id.'">';
		$html .= '<input type="hidden" class="timeinterval-hidden" name="'.$name.'" value="'.$value.'" />';
		$html .= '<input type="text" size="'.$size.'" class="'.$class.' timeinterval-count" value="'.$valueCnt.'"/>';
		$html .= '<select class="timeinterval-type" class="'.$class.' timeinterval-type">';
		$html .= '<option value="day">'.JText::_('COM_NEWSLETTER_DAYS').'</option>';
		$html .= '<option value="week">'.JText::_('COM_NEWSLETTER_WEEKS').'</option>';
		$html .= '</select></div>';

		return $html;
	}		
	
}
