<?php

/**
 * The controller for templates view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Templates controller class.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterControllerTemplates extends JControllerAdmin
{

	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_NEWSLETTER_TEMPLATES';

	/**
	 * Proxy for getModel.
	 * @return  JModel model object
	 * @since	1.0
	 */
	public function getModel($name = 'Template', $prefix = 'NewsletterModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

}