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
		$params = array(
			'scope' => isset($this->element['scope'])? $this->element['scope'] : null
		);
		
		$options = $this->getData($params);

		// Merge any additional options in the XML definition.

		if (empty($options)) {
			$options = array();
		}
		
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_NEWSLETTER_SELECT_NEWSLETTER')));

		return $options;
	}

	
	
	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects (list of available SMTP profiles).
	 * @since	1.0
	 */
	public function getData($options)
	{
		// Initialize variables.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Get full info about the newsletter
		$query->select('DISTINCT newsletter_id AS value, ns.name AS text, (CASE WHEN l.list_id IS NULL THEN 0 ELSE 1 END) AS used_as_static')
			->from('#__newsletter_newsletters AS ns')
			->join('LEFT', '#__newsletter_lists AS l ON (ns.newsletter_id = l.send_at_reg OR ns.newsletter_id = l.send_at_unsubscribe)')
			->where('(category = 0 OR category IS NULL)');
		// Via the SCOPE attribute you can set the scope: all, all static, all ordinary or ordinary_unsent newsletters
		if (!empty($options['scope'])) {

			$scope = explode(' ', $options['scope']);
			
			$where = array();
			
			if (in_array('ordinary', $scope)) {
				$where[] = 'type=0';
			}

			if (in_array('ordinary_unsent', $scope)) {
				$where[] = '(type=0 AND sent_started="0000-00-00 00:00:00")';
			}
			
			if (in_array('static', $scope)) {
				$where[] = 'type=1';
			}

			if (!empty($where)) {
				$query->where('(' . implode(' OR ', $where) . ')');
			}
		}
		$query->order('ns.name');

		// Get the options.
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
