<?php

/**
 * The controller for template json requests.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('migur.library.mailer');

class NewsletterControllerTemplate extends JControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('savenclose', 'save');
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.0
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl = JRequest::getCmd('tmpl', 'component');
		$layout = JRequest::getCmd('layout');
		$append = '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout) {
			$append .= '&layout=' . $layout;
		}

		if ($recordId) {
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	public function getparsed()
	{

		$mailer = new MigurMailer();
		$data = $mailer->getTemplate(array(
			'type' => JRequest::getString('type'),
			't_style_id' => JRequest::getString('t_style_id'),
			'showNames'  => (bool)JRequest::getString('shownames'),
			'tracking'   => false,
			'trackingGa' => false,
			'renderMode' => JRequest::getString('tagsRenderMode')
		));

		//TODO: Need to remove this
		// Remove the <style> section
		$data->content = preg_replace('/<style.*>.*<\/style>/s', '', $data->content);


		$state = (bool) $data;
		$error = (array) $mailer->getErrors();
		echo json_encode(
			array(
				'state' => $state,
				'error' => $error,
				'data' => $data,
			)
		);
	}

}
