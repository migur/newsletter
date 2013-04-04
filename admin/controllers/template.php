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
	protected function allowSave($data = array(), $key = 'id')
	{
		return true;
	}

	
	public function add() {
		
		$this->input->set('layout', 'defailt');
		
		return parent::add();
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
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
	
	/**
	 * 
	 */
	public function getparsed()
	{
		NewsletterHelper::jsonPrepare();

		$mailer = new MigurMailer();
		$data = $mailer->getTemplate(array(
			'type' => JRequest::getString('type'),
			't_style_id' => JRequest::getString('t_style_id'),
			'showNames'  => (bool)JRequest::getString('shownames'),
			'tracking'   => false,
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
		
		NewsletterHelper::jsonResponse((bool) $data, $error, $data);
	}
}

