<?php

/**
 * The controller for template view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

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
	protected function allowSave($data = array(), $key = 'id')
	{
		return true;
	}

	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function save()
	{
		$jform = JRequest::getVar('jform', array(), 'post', 'array');
		$data = $jform;
		unset($data['title']);
		$jform['params'] = $data;
		JRequest::setVar('jform', $jform, 'post');

		if (parent::save()) {
			// Set the redirect based on the task.
			switch ($this->getTask()) {
				case 'save':
					$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
					break;
			}

			return true;
		} else {
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $key) . '&tmpl=component', false));
		}

		return false;
	}

	/**
	 * Save the configuration
	 *
	 * @return void
	 * @since 1.0
	 */
	function create()
	{
		$model = $this->getModel();

		$tpl = JRequest::getString('template');
		$standard = $model->getTemplateBy($tpl);

		$table = JTable::getInstance('Template', 'NewsletterTable');
		$table->save(array(
			'template' => $tpl,
			'title' => $standard->information['name'] . ' (custom)',
			'params' => '{}'
		));
		$this->setRedirect('index.php?option=com_newsletter&view=close&tmpl=component');
	}

}

