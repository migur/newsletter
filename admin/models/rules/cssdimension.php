<?php

/**
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formrule');

/**
 * Form Rule class for the form.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class JFormRuleCssdimension extends JFormRule
{

	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var		string
	 * @since	1.0
	 */
	protected $regex = '^([0-9]+\s*(\%|px|em))|(auto)|$';

}