<?php

/**
 * The file model. Implements the standard functional for file handling.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
//TODO: Move it all to helpers
// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.error.log');

JLoader::import('helpers.media', JPATH_COMPONENT_ADMINISTRATOR, '');

/**
 * Class of model for handling the files.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelFile extends JModel
{

	/**
	 * Upload a file
	 *
	 * @param  array $params - the parameters of uploaded file
	 *
	 * @return array
	 * @since 1.0
	 */
	function upload($params = array())
	{
		// Check for request forgeries
		if (!JRequest::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			return $response;
		}

		// Get the user
		$user = JFactory::getUser();
		$log = JLog::getInstance('upload.error.php');

		// Get some data from the reques
		$filedataName = !empty($params['filedataName']) ? $params['filedataName'] : 'Filedata';
		$file = JRequest::getVar($filedataName, '', 'files', 'array');
		$folder = '/data'; //JRequest::getVar('folder', '/data', '', 'path');
		$return = JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			// The request is valid
			$err = null;

			$filepath = JPath::clean(JPATH_COMPONENT . DS . $folder . DS . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err)) {
				$log->addEntry(array('comment' => 'Invalid: ' . $filepath . ': ' . $err));
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				return $response;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');
			$dispatcher = JDispatcher::getInstance();
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = $dispatcher->trigger('onContentBeforeSave', array('com_media.file', $object_file));
			if (in_array(false, $result, true)) {
				// There are some errors in the plugins
				$log->addEntry(array('comment' => 'Errors before save: ' . $filepath . ' : ' . implode(', ', $object_file->getErrors())));
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				return $response;
			}

			if (JFile::exists($filepath) && empty($params['overwrite'])) {
				// File exists
				$log->addEntry(array('comment' => 'File exists: ' . $filepath . ' by user_id ' . $user->id));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_FILE_EXISTS')
				);
				return $response;
			} elseif (!$user->authorise('core.create', 'com_media')) {
				// File does not exist and user is not authorised to create
				$log->addEntry(array('comment' => 'Create not permitted: ' . $filepath . ' by user_id ' . $user->id));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				return $response;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				// Error in upload
				$log->addEntry(array('comment' => 'Error on upload: ' . $filepath));
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				return $response;
			} else {
				// Trigger the onContentAfterSave event.
				//$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file), null);
				$log->addEntry(array('comment' => $folder));
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen('COM_MEDIA_BASE'))),
					'file' => $file
				);

				return $response;
			}
		} else {
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_MEDIA_ERROR_BAD_REQUEST')
			);

			return $response;
		}
	}

}
