<?php

/**
 * The controller for newsletter json requests.
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

JLoader::import('helpers.autocompleter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.newsletter', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.download', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.html.file', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerNewsletter extends JControllerForm
{
	/**
	 * Get data for JS autocompleter to the client
	 *
	 * @since 1.0
	 */
	public function autocomplete()
	{
		echo json_encode(AutocompleterHelper::getSubscribers());
		jexit();
	}


	/**
	 * Handles the configuration "Clear sent" button
	 * Clear all data from table "_sent".
	 * @return void
	 * @since  1.0
	 */
	public function clearSent()
	{
		$table = JTable::getInstance('sent', 'NewsletterTable');
		$res = $table->deleteAll();
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
		));
		unset($table);
		jexit();
	}

	/**
	 * Handles the unbinding of a file attached to a newsletter
	 *
	 * @return void
	 * @since  1.0
	 */
	public function fileUnbind()
	{

		$id = JRequest::getInt('download_id');
		if (empty($id)) {
			echo json_encode(array(
				'state' => 0,
				'error' => 'Parameters are mising',
			));
			return;
		}

		$table = JTable::getInstance('downloads', 'NewsletterTable');
		$res = $table->delete($id);
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
		));
		jexit();
	}

	/**
	 * Handles the binding of a file attached to a newsletter
	 *
	 * @return void
	 * @since  1.0
	 */
	public function fileAttach()
	{

		$filename = JRequest::getString('filename');
		$nId = JRequest::getInt('newsletter_id');

		if (empty($nId) || empty($filename)) {
			echo json_encode(array(
				'state' => 0,
				'error' => 'Parameters are mising',
			));
			return;
		}

		$mediaParams = JComponentHelper::getParams('com_media');
		$filename = $mediaParams->get('file_path') . DS . $filename;
		$filename = str_replace('/', DS, $filename);
		
		$table = JTable::getInstance('downloads', 'NewsletterTable');
		$res = $table->save(array('filename' => $filename, 'newsletter_id' => $nId));

		$file = new stdClass();

		if ($res) {
			$file->downloads_id = $table->downloads_id;
			$file->newsletter_id = $nId;
			$file->filename = $filename;
			DownloadHelper::getAttributes($file);
			$file->size = JHtml::_('file.size', $file->size, 'kb/mb');
		}
		echo json_encode(array(
			'state' => (int) $res,
			'error' => ($res) ? 'ok' : $table->getErrors(),
			'data' => $file
		));
		jexit();
	}

}

