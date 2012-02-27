<?php

/**
 * The grid helper. Contain the methods to create the controls for grid.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

abstract class JHtmlMultigrid
{

	/**
	 * The multigrid version of a grid.id
	 * 
	 * @param int The row index
	 * @param int The record id
	 * @param boolean
	 * @param string name
	 * @param string The name of the form element
	 * @param boolean disabled or not
	 *
	 * @return string
	 * @since 1.0
	 */
	public static function id($rowNum, $recId, $checkedOut=false, $name='cid', $formName = null, $disabled = false)
	{
		//TODO: Need to create the way to work with multiform page via only one point - submitbutton.js
		if (!$formName) {
			$formName = 'adminForm';
		}

		if ($checkedOut) {
			return '';
		} else {
			return '<input type="checkbox" ' . (($disabled) ? 'disabled="true" readonly="true"' : '') . ' id="cb' . $rowNum . '" name="' . $name . '[]" value="' . $recId . '" onclick="Migur.lists.isChecked(this.checked, \'' . $formName . '\');" title="' . JText::sprintf('JGRID_CHECKBOX_ROW_N', ($rowNum + 1)) . '" />';
		}
	}

	/**
	 * The multigrid version of a grid.sort
	 *
	 * @param int     The column title
	 * @param int     The order
	 * @param string  The direction
	 * @param string  Selected
	 * @param string  The name of task
	 * @param boolean New direction
	 * @param string  The form name
	 *
	 * @return string
	 * @since 1.0
	 */
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task=NULL, $new_direction='asc', $form = null)
	{
		//TODO: Need to create the way to work with multiform page via only one point - submitbutton.js
		$direction = strtolower($direction);
		$images = array('sort_asc.png', 'sort_desc.png');
		$index = intval($direction == 'desc');

		if ($order != $selected) {
			$direction = $new_direction;
		} else {
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$args = "'{$order}','{$direction}','{$task}'";
		if (!empty($form)) {
			$args .= ",'{$form}'";
		}
		$html = '<a href="javascript:Migur.lists.tableOrdering(' . $args . ');" title="' . JText::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN') . '">';
		$html .= JText::_($title);

		if ($order == $selected) {
			$html .= JHTML::_('image', 'system/' . $images[$index], '', NULL, true);
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * Render the options for "status" field
	 *
	 * @param array config
	 *
	 * @return string
	 * @since 1.0
	 */
	public static function enabledOptions($config = array())
	{
		//TODO: Need to create the way to work with multiform page via only one point - submitbutton.js

		// Build the active state filter options.
		$options = array();
		if (!array_key_exists('published', $config) || $config['published']) {
			$options[] = JHtml::_('select.option', '1', 'COM_NEWSLETTER_ACTIVE');
		}
		if (!array_key_exists('unpublished', $config) || $config['unpublished']) {
			$options[] = JHtml::_('select.option', '0', 'COM_NEWSLETTER_INACTIVE');
		}
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[] = JHtml::_('select.option', '*', 'JALL');
		}
		return $options;
	}

	/**
	 * Builds a list of options based on lists array
	 * @param array $lists
	 *
	 * @return array $options
	 * @since 1.0
	 */
	public static function listsOptions($lists = array())
	{
		$options = array();
		// Build the options array
		foreach ($lists as $list) {
			$options[] = JHtml::_('select.option', $list->list_id, $list->name);
		}
		return $options;
	}

	/**
	 * Builds a list of options based on lists array
	 * @param array $lists
	 *
	 * @return array $options
	 * @since 1.0
	 */
	public static function generalOptions($lists = array(), $text = 'text', $value = 'value')
	{
		$options = array();
		// Build the options array
		foreach ($lists as $list) {
			if (!empty($text) && !empty($value)) {
				$list = (array)$list;
				$options[] = JHtml::_('select.option', $list[$value], $list[$text]);
			}	else {
				$options[] = JHtml::_('select.option', $list, $list);
			}
		}
		return $options;
	}

	
	/**
	 * Builds a list of options based on lists array
	 * @param array $lists
	 *
	 * @return array $options
	 * @since 1.0
	 */
	public static function typesOptions($lists = array())
	{
		$data = array(
			array('value' => '1', 'text' => JText::_('COM_NEWSLETTER_MIGURTYPE_SUBSCRIBER')),
			array('value' => '2', 'text' => JText::_('COM_NEWSLETTER_JUSERTYPE_SUBSCRIBER'))
		);
		
		$options = array();
		// Build the options array
		foreach ($data as $item) {
			$options[] = JHtml::_('select.option', $item['value'], $item['text']);
		}
		return $options;
	}
	
	
	/**
	 * Builds a list of options based on lists array
	 * @param array $lists
	 *
	 * @return array $options
	 * @since 1.0
	 */
	public static function templateOptions($config = array())
	{
		// Build the active state filter options.
		$options = array();
		if (!array_key_exists('standard', $config) || $config['standard']) {
			$options[] = JHtml::_('select.option', '1', 'COM_NEWSLETTER_STANDARD');
		}
		if (!array_key_exists('custom', $config) || $config['unpublished']) {
			$options[] = JHtml::_('select.option', '2', 'COM_NEWSLETTER_CUSTOM');
		}
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[] = JHtml::_('select.option', '*', 'JALL');
		}
		return $options;
	}

	/**
	 * Builds a list of options based on lists array
	 *
	 * @param <type> $value
	 * @param <type> $i 
	 * @param <type> $img1
	 * @param <type> $img0
	 * @param <type> $prefix
	 * @param <type> $form - name of form
	 *
	 * @return string
	 * @since 1.0
	 */
	static function enabled($value, $i, $img1 = 'tick.png', $img0 = 'publish_x.png', $prefix='', $form = null)
	{
		if (is_object($value)) {
			$value = $value->published;
		}

		$img = $value ? $img1 : $img0;
		$task = $value ? 'unpublish' : 'publish';
		$alt = $value ? JText::_('COM_NEWSLETTER_ENABLED') : JText::_('COM_NEWSLETTER_DISABLED');
		$action = $value ? JText::_('COM_NEWSLETTER_DISABLE_ITEM') : JText::_('COM_NEWSLETTER_ENABLE_ITEM');

		$args = "'cb{$i}','$prefix$task'";
		if (!empty($form)) {
			$args .= ",'{$form}'";
		}
		$html = '<a href="javascript:void(0);" onclick="return Migur.lists.listItemTask(' . $args . ')" title="' . $action . '">' .
			JHTML::_('image', 'admin/' . $img, $alt, array('border' => 0), true) . '</a>';

		return $html;
	}

	
	static function renderObject($data, $level = 0, $color = 'black') 
	{
		$spaces = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$res = '';
		
		if (is_array($data) || is_object($data)) {
			
			foreach($data as $key => $value) {
				
				if (!is_numeric($key)) {
					
					switch(strtolower($key)) {
						
						case 'error':
						case 'errors':
							$color = 'red';
							break;
						
						default: 
							$color = 'black';
							break;
					}
					$res .= substr($spaces, 0, $level*12) . '<span style="color:'.$color.'">'.$key.'</span>' . ':';
					$res .= (is_array($value) || is_object($value))? '<br/>' : '';
				}
				
				$res .= self::renderObject($value, $level+1, $color);
			}
			
		} else {
			$res .= '<span style="color:'.$color.'">' . substr($spaces, 0, $level*12) . $data . '</span><br/>';
		}
		
		return $res;
	}
	
}
