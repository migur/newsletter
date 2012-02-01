<?php

/**
 * The template model. Implements the standard functional for template view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('migur.library.mailer.document');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Class of template model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelTemplate extends JModelAdmin
{

	protected $_context;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 * @since	1.0
	 */
	public function getTable($type = 'Template', $prefix = 'NewsletterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_newsletter.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.template.data', array());
		if (empty($data)) {
			$data = $this->getItem();
			$arr = $data->getProperties();
			unset($arr['params']['t_style_id']);
			$arr = array_merge($arr, $arr['params']);
			$data->setProperties($arr);
			unset($data->params);
			// Prime some default values.
			/* 			if ($this->getState('template.id') == 0) {
			  $app = JFactory::getApplication();
			  $data->set('catid', JRequest::getInt('catid', $app->getUserState('com_banners.banners.filter.category_id')));
			  }
			 */
		}
		return $data;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string       Script files
	 * @since  1.0
	 */
	public function getScript()
	{
		return 'administrator/components/com_newsletter/models/forms/template.js';
	}

	/**
	 * Method to get the template by it's id.
	 * Can fetch both types standard and custom.
	 * For standard type use the filename of template
	 *
	 * @param String - template id or filename
	 *
	 * @return JObject
	 * @since  1.0
	 * @deprecated
	 */
	public function getTemplateBy($id, $preserve = false)
	{

		//TODO: Check. If this method is not used anywhere then remove it.
		jimport('joomla.utilities.simplexml');

		if (empty($id))
			return false;
		$isCustom = false;
		// Make sure that result will be the same type in both cases
		if (strval(intval($id)) == strval($id)) {
			$isCustom = true;
			$item = $this->getItem($id);
			
			if (empty($item)) {
				return false;
			}
			
			$filename = $item->template;
			
		} else {
			$item = new JObject($this->getTable()->getProperties(1));
			
			if (empty($item)) {
				return false;
			}
			
			$filename = $id;
		}

		$fullfile = JPATH_COMPONENT_ADMINISTRATOR . DS . 'extensions' . DS . 'templates' . DS . $filename;
		if (JFile::exists($fullfile) === false) {
			$this->setError("File $fullfile not found");
			return false;
		}

		try {
			$xml = new JSimpleXML;
			$xml->loadFile($fullfile);
			$str = trim($xml->document->template[0]->_data);
			if (!$preserve) {
				$str = str_replace('<position', '<div class="drop container-draggables"', $str);
			}

			$item->filename = $filename;
			$item->content = $str;

			$item->information = array(
				'name' => $xml->document->information[0]->name[0]->_data,
				'author' => $xml->document->information[0]->author[0]->_data,
				'creationDate' => $xml->document->information[0]->creationDate[0]->_data,
				'copyright' => $xml->document->information[0]->copyright[0]->_data,
				'license' => $xml->document->information[0]->license[0]->_data,
				'authorEmail' => $xml->document->information[0]->authorEmail[0]->_data,
				'authorUrl' => $xml->document->information[0]->authorUrl[0]->_data,
				'version' => $xml->document->information[0]->version[0]->_data,
				'description' => $xml->document->information[0]->description[0]->_data
			);
			unset($xml);
		} catch (Exception $e) {
			$this->setError($e->__toString());
			return false;
		}
		return $item;
	}

	/**
	 * Gets the rendered template.
	 *
	 * @param  mixed  $id   template id
	 * @param  string $mode
	 * @return object
	 */
	public function getRenderedBy($id, $mode = 'schematic')
	{

		//TODO: Check. If this method is not used anywhere then remove it.
		$params = array(
			't_style_id' => $id,
			'renderMode' => $mode
		);

		$document = MigurMailerDocument::getInstance('html', $params);
		$template = $document->parse($params);
		$template['content'] = $document->render(false, $params);
		return $template;
	}

	/**
	 * Get all column placeholders from templice.
	 * Something like table_width1 or table_height2.
	 * 
	 * @param string $tid Filename of template
	 * 
	 * @return array Array of names of placeholdedrs
	 */
	public function getColumnPlaceholders($content) 
	{
		$placeholders = PlaceholderHelper::fetchFromString($content);

		$res = array();
		// Let's find column placeholders
		foreach($placeholders as $ph) {
			if(preg_match('/^(width_column.*)|(height_column.*)$/', $ph)) {
				$res[] = $ph;
			}
		}
		
		return $res;
	}
}
