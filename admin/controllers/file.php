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

class NewsletterControllerFile extends MigurController
{
	
	public function fileinfo()
	{
		NewsletterHelperNewsletter::jsonPrepare();
		
		$filename = JRequest::getString('filename');
		$size = @getimagesize($filename);
		if (empty($size)) {
			$size = array(
				'3' => 'width="auto" height="auto"',
				"mime" => "image"
			);
		}
		
		NewsletterHelperNewsletter::jsonMessage(null, $size);
	}

	
	
}
