<?php

/**
 * The newsletter model. Implements the standard functional for newsletter view.
 *
 * @version	   $Id:  $
 * @copyright  Copyright (C) 2011 Migur Ltd. All rights reserved.
 * @license	   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Class of newsletter model of the component.
 *
 * @since   1.0
 * @package Migur.Newsletter
 */
class NewsletterModelNewsletter extends JModelAdmin
{
	protected $_data;

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
	public function getTable($type = 'Newsletter', $prefix = 'NewsletterTable', $config = array())
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
		$form = $this->loadForm('com_newsletter.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_newsletter.edit.newsletter.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Gets the list of plugins used in newsletter
	 *
	 * @param  integer $nid
	 * @param  string  $namespace May be used as filter
	 *
	 * @return array of objects
	 * @since  1.0
	 */
	static public function getUsedPlugins($nid, $namespace = '')
	{
		if (empty($nid)) {
			return array();
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('ne.*, e.extension, e.namespace');
		$query->from('#__newsletter_extensions AS e');
		$query->join('', '#__newsletter_newsletters_ext AS ne ON e.extension_id = ne.extension_id');
		$query->where('ne.newsletter_id='.$db->quote((int)$nid));
		$query->where('e.type = "2"');

		// Set the query
		$db->setQuery($query);
		$objs = $db->loadObjectList();

		$res = array();
		foreach($objs as $obj) {

			// Check if plugin is included into NAMESPACE and anabled for this letter
			if (NewsletterHelperPlugin::namespaceCheckOccurence($namespace, $obj->namespace)) {
				$obj->params = (object) json_decode($obj->params);
				if (!empty($obj->params->active)) {
					$res[] = $obj;
				}
			}
		}
		return $res;
	}

	/**
	 * Tells us if we can update (save) the newsletter.
	 * We can save it if it is static or if it has not sent earlier.
	 *
	 * @param JObject|int $newsletter
	 *
	 * @return	boolean
	 */
	public function isUpdateAllowed($newsletter)
	{
		if (!is_object($newsletter) && !is_numeric($newsletter)) {
			throw new Exception('isUpdateAllowed. Invalid newsletter identifacation');
		}

		if (is_numeric($newsletter)) {
			$newsletter = $this->getItem((int)$newsletter)->newsletter_id;
		}

		if (empty($newsletter)) {
			throw new Exception('isUpdateAllowed. Newsletter absent');
		}

		return (empty($newsletter->newsletter_id) || $newsletter->type == 1/*Static newsletter*/ || $newsletter->sent_started == '0000-00-00 00:00:00');
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   13.08
	 */
	protected function canDelete($record)
	{
		return NewsletterHelperAcl::actionIsAllowed('newsletter.edit');
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   13.08
	 */
	protected function canEditState($record)
	{
		return NewsletterHelperAcl::actionIsAllowed('newsletter.edit');
	}
}
