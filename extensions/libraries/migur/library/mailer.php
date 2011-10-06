<?php

/**
 * The helper for rendering and mailing the newsletters
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
//TODO: Move all this functionality to the helper
// no direct access
defined('_JEXEC') or die;

jimport('migur.library.mailer.document');
jimport('migur.library.mailer.sender');

JLoader::import('helpers.subscriber', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.mail', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('helpers.download', JPATH_COMPONENT_ADMINISTRATOR, '');
JLoader::import('tables.history', JPATH_COMPONENT_ADMINISTRATOR, '');
jimport('joomla.error.log');

/**
 * Class for rendering and mailing the newsletters
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class MigurMailer extends JObject
{

	/**
	 * Create the result mail content.
	 * Can parse the newsletter or the template
	 *
	 * @param   array  $params - The peremeters
	 *          type *          - type of the letter (html, plain)
	 *          directory       - the directory to find the extensions
	 *          renderMode      - render mode (full, schematic, raw)
	 *          template        - the content of template to use
	 *          newsletter_id * - the id of the newsletter
	 *          t_style_id      - the id of the template
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function render($params)
	{
		// If can't determine the type of doc...
		if (empty($params['type']) || !in_array($params['type'], array('html', 'plain'))) {
			$this->setError('Type is not defined');
			return false;
		}

		$document = MigurMailerDocument::getInstance($params['type'], $params);
		//$this->triggerEvent('onMailerBeforeRender');
		$data = $document->render(false, $params);
		//$this->triggerEvent('onMailerAfterRender');
		//var_dump($data);// die();
		unset($document);

		return $data;
	}

	/**
	 * Render each module from the list.
	 *
	 * @param <type> $params - the array(object(widgetId, moduleName, native))
	 * @return <type>
	 */
	public function renderModules($params)
	{


		MigurModuleHelper::renderModule($module);

		// If can't determine the type of doc...
		if (empty($params['type']) || !in_array($params['type'], array('html', 'plain'))) {
			$this->setError('Type is not defined');
			return false;
		}

		$document = MigurMailerDocument::getInstance($params['type'], $params);
		//$this->triggerEvent('onMailerBeforeRender');
		$data = $document->render(false, $params);
		//$this->triggerEvent('onMailerAfterRender');
		//var_dump($data);// die();
		unset($document);

		return $data;
	}

	/**
	 * Get parse only Template standard or custom.
	 *
	 * @param   array  $params - The type, t_style_id, rules
	 * 
	 * @return	string The template name
	 * @since	1.0
	 */
	public function getTemplate($params = false)
	{
		// render the content of letter for each user
		// If can't determine the type of doc...
		if (empty($params['type']) || !in_array($params['type'], array('html', 'plain'))) {
			$this->setError('Type is not defined');
			return false;
		}

		/* if we get int then this is the custom template (schematic mode),
		 * otherwise set raw mode
		 */
		$params['renderMode'] = ($params['t_style_id'] == strval(intval($params['t_style_id']))) ? 'schematic' : 'raw';
		//var_dump($params, intval($params['t_style_id'])); die();
		$document = MigurMailerDocument::getInstance($params['type'], $params);
		//$this->triggerEvent('onMailerBeforeRender');
		$document->render(false, $params);
		$tpl = $document->getTemplate();
		//$this->triggerEvent('onMailerAfterRender');
		unset($document);

		return $tpl;
	}

	/**
	 * The main send of one letter to one or mode recipients.
	 * The mail content generates for each user
	 *
	 * @param  array $params the [smtpProfile], [letter], [emails]
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function sendToList($params = null)
	{
		// load letter to send....
		$letter = MailHelper::loadLetter($params['newsletter_id']);
		if (empty($letter->newsletter_id)) {
			$this->setError('Lading letter error or newsletter_id is not defined');
			return false;
		}

		$sender = new MigurMailerSender();

		SubscriberHelper::saveRealUser();

		// Get attachments
		$atts = DownloadHelper::getByNewsletterId($params['newsletter_id']);
		
		// Main mailing cycle
		$res = true;
		foreach ($params['subscribers'] as $item) {

			$this->set('_errors', array());

			$type = MailHelper::filterType(
					!empty($params['type']) ? $params['type'] : null
			);
			if (!$type) {
				if (!($type = MailHelper::filterType(
						!is_null($item->html) ? (($item->html == 1) ? 'html' : 'plain') : null)
					)) {
					$this->setError('The type "' . $type . '" is not supported');
					$res = false;
					break;
				}
			}

			// emulate user environment
			if (!SubscriberHelper::emulateUser(array('email' => $item->email))) {
				$this->setError('The user ' . $item->email . ' is absent');
				$res = false;
				break;
			}
			
			PlaceholderHelper::setPlaceholder('newsletter id', $letter->newsletter_id);
			
			// render the content of letter for each user
			$letter->content = $this->render(array(
					'type' => $type,
					'newsletter_id' => $letter->newsletter_id,
					'tracking' => true
				));

			if ($letter->content === false) {
				$res = false;
				break;
			}

			//Add custom headers
			$sender->AddCustomHeader('Email-Name:' . $letter->name);
			$sender->AddCustomHeader('Subscriber-ID:' . $item->subscriber_id);

			// send the unique letter to each recipient
			$bounced = $sender->send(array(
					'letter' => $letter,
					'attach' => $atts,
					'emails' => array($item),
					'smtpProfile' => $letter->smtp_profile,
					'type' => $type,
					'tracking' => $params['tracking']
				));

			// If sending failed
			if (!$bounced) {

				$this->setError(JError::getError('unset')->get('message'));
				$res = false;
			}
		}

		SubscriberHelper::restoreRealUser();
		return $res;
	}

	/**
	 * The main send of one letter to one or mode recipients.
	 * The mail content generates for each user
	 *
	 * @param  array $params the letter, subscriber, type
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function send($params = null)
	{
		// load letter to send....
		$letter = MailHelper::loadLetter($params['newsletter_id']);
		if (empty($letter->newsletter_id)) {
			$this->setError('Lading letter error or newsletter_id is not defined');
			return false;
		}

		$sender = new MigurMailerSender();

		// Result object
		$res = new StdClass();
		$res->state = false;

		$subscriber = $params['subscriber'];
		$type = MailHelper::filterType(!empty($params['type']) ? $params['type'] : null);
		if (!$type) {
			$res->error = 'The type "' . $type . '" is not supported';
			return $res;
		}

		// emulate user environment
		SubscriberHelper::saveRealUser();

		if (!SubscriberHelper::emulateUser(array('email' => $subscriber->email))) {
			$res->error = 'The user ' . $subscriber->email . ' is absent';
			return $res;
		}

		PlaceholderHelper::setPlaceholder('newsletter id', $letter->newsletter_id);
		
		// render the content of letter for each user
		$letter->content = $this->render(array(
				'type' => $type,
				'newsletter_id' => $letter->newsletter_id,
				'tracking' => true
			));

		if ($letter->content === false) {
			return $res;
		}

		//Add custom headers
		$sender->AddCustomHeader('Email-Name', $letter->name);
		$sender->AddCustomHeader('Subscriber-ID', $subscriber->subscriber_id);
		
		// Get attachments
		$atts = DownloadHelper::getByNewsletterId($params['newsletter_id']);
		
		// send the unique letter to each recipient
		$sendRes = $sender->send(array(
				'letter' => $letter,
				'attach' => $atts,
				'emails' => array($subscriber),
				'smtpProfile' => $letter->smtp_profile,
				'type' => $type,
				'tracking' => $params['tracking']
			));

		// If sending failed
		if (!$sendRes) {
			$res->error = JError::getError('unset')->get('message');
			return $res;
		}

		SubscriberHelper::restoreRealUser();

		$res->content = $letter->content;
		$res->state = true;
		return $res;
	}

}
