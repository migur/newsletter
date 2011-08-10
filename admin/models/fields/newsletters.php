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
class JFormFieldNewsletters extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $type = 'newsletters';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of available SMTP profiles).
	 * @since	1.0
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get full info about the newsletter
		$query->select('DISTINCT newsletter_id AS value, ns.name AS text, (CASE WHEN l.list_id IS NULL THEN 0 ELSE 1 END) AS used_as_static')
			->from('#__newsletter_newsletters AS ns')
			->join('LEFT', '#__newsletter_lists AS l ON (ns.newsletter_id = l.send_at_reg OR ns.newsletter_id = l.send_at_unsubscribe)');

		// Via the SCOPE attribute you can set the scope: all, all static, static_unused, all ordinary or ordinary_unused newsletters
		if (!empty($this->element['scope'])) {

			if ($this->element['scope'] == 'ordinary') {
				$query->where('type=0');
			}

			if ($this->element['scope'] == 'ordinary_unused') {
				$query->where('type=0 AND sent_started="0000-00-00 00:00:00"');
			}
			
			if ($this->element['scope'] == 'static') {
				$query->where('type=1');
			}

			if ($this->element['scope'] == 'static_unused') {
				$query->where('type=1 AND used_as_static=0');
			}
		}
		//echo $this->element['scope']; die();
		$query->order('ns.name');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		//$options = array_merge(parent::getOptions(), $options);

		if (empty($options))
			$options = array();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_NEWSLETTER_SELECT_NEWSLETTER')));

		return $options;
	}

}
