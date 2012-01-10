<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of HelloWorld component
 */
class com_newsletterInstallerScript
{
	/**
	 * Suffix for the table
	 * @var string
	 */
	static public $backSuffix = '_bak';

	/**
	 * The namespace of the component's tables
	 * @var string
	 */
	static public $comNamespace = '_newsletter_';

	/**
	 * method to install the component
	 *
	 * @param  object - parent
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function install($parent)
	{
		return true;
	}

	/**
	 * method to uninstall the component
	 *
	 * @param  object - parent
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function uninstall($parent)
	{
		return true;
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param  string - type of upgrade
	 * @param  object - parent
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function preflight($type, $parent)
	{
		// if "install" then J! cant find the component.
		// So we only backup tables if they are present
		if ($type == 'install') {

			// Try to find the tables to backup
			$tables = $this->_getComponentTables();

			if (!empty($tables)) {
				$this->backedup = array();
				foreach ($tables as $table) {

					$tableBacked = $this->_backupTable($table);

					if ($tableBacked) {

						$this->backedup[] = array(
							'original' => $table,
							'backup'   => $tableBacked
						);

					} else {
						return false;
					}
				}
			}
			return true;
		}

		
		if ($type == 'update') {
			
			// Check the component version 
			$extensionTable = JTable::getInstance('Extension', 'JTable');
			
			$extensionTable->load(array(
				'type'    => 'component',
				'element' => 'com_newsletter'));

			if (!empty($extensionTable->extension_id)) {
				$manifestOld = json_decode($extensionTable->manifest_cache);
				$manifestNew = $parent->getParent()->getManifest();
				
				$res = version_compare(
					(string)$manifestNew->version, 
					(string)$manifestOld->version);

				// If the fist is greater than second
				if ($res < 0) {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_NEWSLETTER_SYSTEM_ALREADY_HAS_NEWER_VERSION'), 'error');
					return false;
				}
			}
			return true;
		}
		
		return false;
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param  string - type of upgrade
	 * @param  object - parent
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function postflight($type, $parent)
	{
            // In both cases check if the tables/extension.php is not exists!
		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'tables'.DS.'extension.php');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3b.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3b2.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3b3.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3c.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3d.sql');
//		@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.4a.sql');
			
            //error_reporting(E_ALL);
            //ini_set('display_errors', 1);
            /* Dirty hack. Changes the type of the update adapter for sites of com_newsletter 
               to able to update this component via J! Updater */
            if ($type == 'update') {

                $dbo = JFactory::getDbo();
                
                $dbo->setQuery(
                    'SELECT juse.update_site_id as id '.
                    'FROM #__extensions AS e '.
                    'JOIN #__update_sites_extensions AS juse ON juse.extension_id = e.extension_id '.
                    'JOIN #__update_sites AS us ON juse.update_site_id = us.update_site_id '.
                    'WHERE e.element = "com_newsletter"'
                );
                $res = $dbo->loadAssocList();
                
                if (!empty($res)) {
                    $arr = array();
                    foreach($res as $item) {
                        $arr[] = $item['id'];
                    }
                    $dbo->setQuery('UPDATE #__update_sites SET type="extension" WHERE update_site_id in ('.implode(',', $arr).')');
                    $dbo->query();
                }    
            }

            // Let's store the info about backed up tables
            if (!empty($this->tables)) {
                    $sess = JFactory::getSession();
                    $sess->set('com-newsletter-backup', $this->backedup);
            }

            /* Redirect after installation. Make sure the component was installed the last if
               there is package */
            JInstaller::getInstance()->setRedirectURL('index.php?option=com_newsletter&view=wellcome');
            return true;
	}

	/**
	 * Gets the array of component's tables
	 * 
	 * @return array
	 * @since  1.0
	 */
	function _getComponentTables()
	{
		$dbo = JFactory::getDbo();
		$sql = 'SHOW TABLES;';
		$dbo->setQuery($sql);
		$res = $dbo->query();

		if (empty($res)) {
			return array();
		}

		$tables = array();
		$this->tables = array();

		while ($row = mysql_fetch_row($res)) {

			if (!empty($row[0]) && !empty(self::$comNamespace) && strpos($row[0], self::$comNamespace) !== false) {

				$this->tables[] = $row[0];

				if (strpos($row[0], self::$backSuffix) === false) {
					$tables[] = $row[0];
				}
			}
		}
		return $tables;
	}

	/**
	 * Backs up the table.
	 *
	 * @param string - the table name
	 * @param string - the new number of backed up table
	 *
	 * @return boolean
	 * @since  1.0
	 */
	function _backupTable($table)
	{
		$cnt = $this->_getCountForTable($table);

		$dbo = JFactory::getDbo();
		$tableNew = $table . self::$backSuffix . $cnt;
		
		//Delete all foreign keys
		$sql = 'SHOW CREATE TABLE ' . $table . ';';
		$dbo->setQuery($sql);
		$res = $dbo->query();
		$res = mysql_fetch_array($res);
		$text = $res['Create Table'];
		
		$matches = array();
		preg_match_all('/CONSTRAINT[\s]+\`([^\`]+)\`[\s]+FOREIGN\sKEY/', $text, $matches);
		
		if (!empty($matches[1])) {
			foreach($matches[1] as $fkey) {
				$sql = 'ALTER TABLE ' . $table . ' DROP FOREIGN KEY ' . $fkey . ';';
				$dbo->setQuery($sql);
				$dbo->query();
			}
		}		
		
		// Rename the table
		$sql = 'RENAME TABLE ' . $table . ' TO ' . $tableNew . ';';
		$dbo->setQuery($sql);
		$res = $dbo->query();
		return $tableNew;
	}

	/**
	 * Gets the count for the table to backup.
	 *
	 * @param string - the table name
	 *
	 * @return integer
	 * @since  1.0
	 */
	function _getCountForTable($table)
	{
		$nameTpl = $table . self::$backSuffix;
		$tables = implode(' ', $this->tables);

		$matches = array();
		preg_match_all('/' . $nameTpl . '([\d]{1,4})/', $tables, $matches);

		$idx = 0;
		foreach ($matches[1] as $match) {
			$match = (int) $match;
			if ($match >= $idx) {
				$idx = $match + 1;
			}
		}
		return $idx;
	}
}