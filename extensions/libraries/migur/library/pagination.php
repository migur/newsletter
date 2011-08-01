<?php

/**
 * Extends the functionality of Pagination. Allow to use multiform pagination.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */

//TODO: It must be removed. The multiform functionality should be implemented
// with submitbutton.js

// No direct access
defined('JPATH_BASE') or die;

// Check if Migur is active
if (!defined('MIGUR')) {
	die(JError::raiseWarning(0, JText::_("MIGUR library wasn't found.")));
}

jimport('joomla.html.pagination');

/**
 * Class for extending the pagination functionality
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurPagination extends JPagination
{

	/**
	 * Creates a dropdown box for selecting how many records to show per page.
	 *
	 * @return	string	The html for the limit # input box.
	 * @since	1.0
	 */
	public function getLimitBox()
	{
		$app = JFactory::getApplication();

		// Initialise variables.
		$limits = array();

		// Make the option list.
		for ($i = 5; $i <= 30; $i += 5) {
			$limits[] = JHtml::_('select.option', "$i");
		}
		$limits[] = JHtml::_('select.option', '50', JText::_('J50'));
		$limits[] = JHtml::_('select.option', '100', JText::_('J100'));
		$limits[] = JHtml::_('select.option', '0', JText::_('JALL'));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Get model name
		$formName = $this->model->getName() . 'Form';
		// Build the select list.
		if ($app->isAdmin()) {
			$html = JHtml::_('select.genericlist', $limits, $this->prefix . 'limit', 'class="inputbox" size="1" onchange="Joomla.submitform(\'\', document.' . $formName . ');"', 'value', 'text', $selected);
		} else {
			$html = JHtml::_('select.genericlist', $limits, $this->prefix . 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}

		return $html;
	}

	protected function _item_active(&$item)
	{
		$formName = $this->model->getName() . 'Form';
		$app = JFactory::getApplication();
		if ($app->isAdmin()) {
			if ($item->base > 0) {
				return "<a title=\"" . $item->text . "\" onclick=\"javascript: document.{$formName}.." . $this->prefix . "limitstart.value=" . $item->base . "; Joomla.submitform('', document.{$formName});return false;\">" . $item->text . "</a>";
			} else {
				return "<a title=\"" . $item->text . "\" onclick=\"javascript: document.{$formName}.." . $this->prefix . "limitstart.value=0; Joomla.submitform('', document.{$formName});return false;\">" . $item->text . "</a>";
			}
		} else {
			return "<a title=\"" . $item->text . "\" href=\"" . $item->link . "\" class=\"pagenav\">" . $item->text . "</a>";
		}
	}

	/**
	 * Create and return the pagination data object.
	 *
	 * @return	object	Pagination data object.
	 * @since	1.0
	 */
	protected function _buildDataObject()
	{
		// Initialise variables.
		$data = new stdClass();

		// Build the additional URL parameters string.
		$params = '';
		if (!empty($this->_additionalUrlParams)) {
			foreach ($this->_additionalUrlParams as $key => $value) {
				$params .= '&' . $key . '=' . $value;
			}
		}
		$data->all = new JPaginationObject(JText::_('JLIB_HTML_VIEW_ALL'), $this->prefix);
		if (!$this->_viewall) {
			$data->all->base = '0';
			$data->all->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=');
		}

		// Set the start and previous data objects.
		$data->start = new JPaginationObject(JText::_('JLIB_HTML_START'), $this->prefix);
		$data->previous = new JPaginationObject(JText::_('JPREV'), $this->prefix);

		if ($this->get('pages.current') > 1) {
			$page = ($this->get('pages.current') - 2) * $this->limit;

			//$page = $page == 0 ? '' : $page; //set the empty for removal from route

			$data->start->base = '0';
			$data->start->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=0');
			$data->previous->base = $page;
			$data->previous->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=' . $page);
		}

		// Set the next and end data objects.
		$data->next = new JPaginationObject(JText::_('JNEXT'), $this->prefix);
		$data->end = new JPaginationObject(JText::_('JLIB_HTML_END'), $this->prefix);

		if ($this->get('pages.current') < $this->get('pages.total')) {
			$next = $this->get('pages.current') * $this->limit;
			$end = ($this->get('pages.total') - 1) * $this->limit;

			$data->next->base = $next;
			$data->next->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=' . $next);
			$data->end->base = $end;
			$data->end->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=' . $end);
		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.start'); $i <= $stop; $i++) {
			$offset = ($i - 1) * $this->limit;

			//$offset = $offset == 0 ? '' : $offset;  //set the empty for removal from route

			$data->pages[$i] = new JPaginationObject($i, $this->prefix);
			if ($i != $this->get('pages.current') || $this->_viewall) {
				$data->pages[$i]->base = $offset;
				$data->pages[$i]->link = JRoute::_($params . '&' . $this->prefix . 'limitstart=' . $offset);
			}
		}
		return $data;
	}

	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x.
	 *
	 * @return	string	Pagination page list string.
	 * @since	1.0
	 */
	public function getPagesLinks()
	{
		$html = parent::getPagesLinks();
		$formName = $this->model->getName() . 'Form';

		$html = str_replace('adminForm', $formName, $html);
		return str_replace(
			'Joomla.submitform()',
			"Joomla.submitform('', document.{$formName})",
			$html);
	}
}
