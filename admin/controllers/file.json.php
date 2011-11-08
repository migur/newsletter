<?php
/**
 * The controller for file json requests.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
JLoader::import('helpers.media', JPATH_COMPONENT_ADMINISTRATOR, '');

class NewsletterControllerFile extends JController {

	/**
	 * Upload a file
	 * @return void
	 * @since 1.5
	 */
	function upload() {
		return;
		// Check for request forgeries
		if (!JRequest::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user = JFactory::getUser();

		// Get some data from the request
		$file = JRequest::getVar('Filedata', '', 'files', 'array');
		$folder = JRequest::getVar('folder', '', '', 'path');
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
				
				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);
				echo json_encode($response);
				return;
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
				echo json_encode($response);
				return;
			}

			if (JFile::exists($filepath)) {
				// File exists
				
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			} elseif (!$user->authorise('core.create', 'com_media')) {
				
				// File does not exist and user is not authorised to create
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				
				// Error in upload
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			} else {
				// Trigger the onContentAfterSave event.
				//$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file), null);
				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen('COM_MEDIA_BASE')))
				);
				echo json_encode($response);
				return;
			}
		} else {
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}

	public function fileinfo() {

		$filename = JRequest::getString('filename');
		$size = @getimagesize($filename);
		if (empty($size)) {
			$size = array(
				'3' => 'width="auto" height="auto"',
				"mime" => "image"
			);
		}
		jexit(json_encode($size));
	}

}
