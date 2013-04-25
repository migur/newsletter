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
class NewsletterModelFile extends MigurModel
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

		// Get some data from the reques
		$filedataName = !empty($params['filedataName']) ? $params['filedataName'] : 'Filedata';
		$file = JRequest::getVar($filedataName, '', 'files', 'array');
		$folder = '/data'; //JRequest::getVar('folder', '/data', '', 'path');
		$return = JRequest::getVar('return-url', null, 'post', 'base64');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		
		$remapped = array();
		foreach($file as $name => $val) {
			$remapped[$name] = $val[0];
		}
		$file = $remapped;

		// Make the filename safe
		//$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name'])) {

			// The request is valid
			$err = null;

			$filepath = JPath::clean(JPATH_COMPONENT . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err)) {
				NewsletterHelperLog::addError(
					'COM_NEWSLETTER_ERROR_CANNOT_UPLOAD_FILE', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath, 'error' => $err)
				);
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
				NewsletterHelperLog::addError(
					'COM_NEWSLETTER_ERROR_BEFORE_SAVE', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath, 'errors' => implode(', ', $object_file->getErrors()))
				);
				
				$response = array(
					'status' => '0',
					'error' => JText::plural('COM_NEWSLETTER_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				return $response;
			}

			if (JFile::exists($filepath) && empty($params['overwrite'])) {
				// File exists
				NewsletterHelperLog::addError(
					'COM_NEWSLETTER_ERROR_FILE_EXISTS', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath,  'user_id' => $user->id)
				);
				
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_NEWSLETTER_ERROR_FILE_EXISTS')
				);
				return $response;
			} elseif (!$user->authorise('core.create', 'com_media')) {
				// File does not exist and user is not authorised to create
				NewsletterHelperLog::addError(
					'COM_NEWSLETTER_ERROR_CREATE_NOT_PERMITTED', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath,  'user_id' => $user->id)
				);

				$response = array(
					'status' => '0',
					'error' => JText::_('COM_NEWSLETTER_ERROR_CREATE_NOT_PERMITTED')
				);
				return $response;
			}

			$file = (array) $object_file;
			if (!JFile::upload($file['tmp_name'], $file['filepath'])) {
				// Error in upload
				NewsletterHelperLog::addError(
					'COM_NEWSLETTER_ERROR_UNABLE_TO_UPLOAD_FILE', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath)
				);
				$response = array(
					'status' => '0',
					'error' => JText::_('COM_NEWSLETTER_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				return $response;
			} else {
				NewsletterHelperLog::addDebug(
					'COM_NEWSLETTER_UPLOAD_COMPLETE', 
					NewsletterHelperLog::CAT_UPLOAD,
					array('file' => $filepath)
				);

				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_NEWSLETTER_UPLOAD_COMPLETE', substr($file['filepath'], strlen('COM_NEWSLETTER_BASE'))),
					'file' => $file
				);

				return $response;
			}
		} else {
			NewsletterHelperLog::addError(JText::_('COM_MEDIA_ERROR_BAD_REQUEST'), NewsletterHelperLog::CAT_LIST);
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_NEWSLETTER_ERROR_BAD_REQUEST')
			);

			return $response;
		}
	}

}
