<?php

/**
 * The newsletter view file.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('migur.library.toolbar');
jimport('joomla.utilities.simplexml');

//TODO: json.view need to remove

/**
 * Class of the newsletter view. Displays the model data.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterViewNewsletter extends MigurView
{

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Displays the view.
	 *
	 * @param  string $tpl the template name
	 *
	 * @return void
	 * @since  1.0
	 */
	public function display($tpl = null)
	{
		$errs = JUser::getInstance()->getErrors();
		$nlid = JUser::getInstance()->getParam('newsletter_id', '');
		$this->state = array(
			'state' => (!empty($errs)) ? $errs : '0',
			'newsletter_id' => $nlid,
			'method' => "newsletter.view.json.php!!!!"
		);

		parent::display('json');
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 * @since 1.0
	 */
	protected function setDocument()
	{
		// Get the document object.
		$document = & JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $view->getName() . '.json"');
	}

}
