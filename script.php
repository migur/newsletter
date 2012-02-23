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
		// Let's notice about extensions that may need to be uninstalled too
		$extensions = $this->_getComponentDependentExtensions();

		if (count($extensions) > 0) {
			
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_NEWSLETTER_EXTENSION_TO_UNINSTALL_FOUND'));

			foreach($extensions as $ext) {
				$app->enqueueMessage(ucfirst($ext->type) . ' '. $ext->name . '(' . $ext->element . ')');
			}
		}
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
		try {
			$this->parent = $parent;
			
			if ($type == 'install') { 
				return $this->_preflightInstall(); 
			}
			if ($type == 'update')  { 
				return $this->_preflightUpdate(); 
			}
			
			return false;
			
		} catch	(Exception $e) {
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}

	
	
	protected function _preflightInstall()
	{
			// if "install" then J! cant find the component.
			// So we only backup tables if they are present

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
						throw new Exception ();
					}
				}
			}
			return true;
	}
	
	
	
	protected function _preflightUpdate()
	{
		// Check if version on the way to install has smaller older
		// version than already installed one
		$this->_checkVersion();

		// Fixes on the fly...
		$this->_fixAllBefore12_01();
		
		return true;
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

			
			// Populate and check initial required data in DB
			$this->_setInitialData();
			
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

	
	/**
	 * Preforms check and restore/populate initial data into DB
	 */
	protected function _setInitialData() 
	{
		// Check/set the record for J! SMTP profile in SMTP_PROFILES table 
//		$dbo = JFactory::getDbo();
//		$dbo->setQuery('select * from #__newsletter_smtp_profiles where is_joomla=1');
//		$list = $dbo->loadObjectList();
//		
//		if (count($list) == 0) {
//			
//			$dbo->setQuery(
//				'INSERT INTO #__newsletter_smtp_profiles SET '.
//				'params="{\"periodLength\":\"60\",\"sentsPerPeriodLimit\":\"100\",\"inProcess\":0,\"periodStartTime\":0,\"sentsPerLastPeriod\":0}", '.
//				'is_joomla=1, '.
//				'mailbox_profile_id=-1, '.
//				'pop_before_smtp="0"');
//			$dbo->query();
//		}
	}

	protected function _checkVersion()
	{
		// Check the component version 
		$extensionTable = JTable::getInstance('Extension', 'JTable');

		$extensionTable->load(array(
			'type'    => 'component',
			'element' => 'com_newsletter'));

		if (!empty($extensionTable->extension_id)) {
			$manifestOld = json_decode($extensionTable->manifest_cache);
			$manifestNew = $this->parent->getParent()->getManifest();

			$res = version_compare(
				(string)$manifestNew->version, 
				(string)$manifestOld->version);

				// If the fist is greater than second
				if ($res < 0) {
					throw new Exception(JText::_('COM_NEWSLETTER_SYSTEM_ALREADY_HAS_NEWER_VERSION'));
				}
		}
	}

	
	
	/**
	 * Fix all issues with DB fond before 12.01
	 * Added in 12.01.
	 * Will be applied only once because emmidiately after fixing
	 * last used schema will be set to 12.01a.
	 * 
	 * @since 12.01
	 */
	protected function _fixAllBefore12_01()
	{
		$res = $this->_getLastUsedSchema();
		
		// If last used patch is older than 12.01 then we need to apply some fixes
		if (!empty($res['version_id']) && version_compare($res['version_id'], '12.01a') < 0) {

			$this->_fixVersion1_0_2b();
			
			$this->_fixVersion1_0_3b();
			
		}
	}
	


	/**
	 * Drops all foreign keys from #_newsletter_sub_history and #_newsletter_sub_list.
	 * These FKs will be recreated in 12.01a patch
	 * 
	 * @since 12.01
	 */
	protected function _fixVersion1_0_2b()
	{
		//
		$fkNames = $this->_getFKNames('#__newsletter_sub_history');
		$this->_removeFKs('#__newsletter_sub_history', $fkNames);

		$fkNames = $this->_getFKNames('#__newsletter_sub_list');
		$this->_removeFKs('#__newsletter_sub_list', $fkNames);
		// Fixed :)
	}

	
	
	/**
 	 * Check and fix the schema 1.0.3.
 	 * Caused bad naming of update patch 1.0.3 that should be named 1.0.3a.
 	 * 
 	 * If last used schema is 1.0.3 that this means that 
 	 * last release was 1.0.3b and this schema should be fixed to 1.0.3a.
	 * 
	 * @since 12.01
	 */
	protected function _fixVersion1_0_3b()
	{

		$res = $this->_getLastUsedSchema();
		if (!empty($res['version_id']) && $res['version_id'] == '1.0.3') {
		
			@unlink(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_newsletter'.DS.'install'.DS.'updates'.DS.'1.0.3.sql');
		
			$dbo = JFactory::getDbo();

			$dbo->setQuery('ALTER TABLE `'.$dbo->getPrefix().'newsletter_queue` ADD INDEX `newsletter_queue_state`(`state`)');
			$dbo->query();

			$dbo->setQuery('ALTER TABLE `'.$dbo->getPrefix().'newsletter_mailbox_profiles` ADD COLUMN `data` TEXT');
			$dbo->query();

			$dbo->setQuery('ALTER TABLE `'.$dbo->getPrefix().'newsletter_smtp_profiles` ADD COLUMN `mailbox_profile_id` INT(11)');
			$dbo->query();

			$dbo->setQuery('ALTER TABLE `'.$dbo->getPrefix().'newsletter_smtp_profiles` MODIFY COLUMN `is_ssl` INT(11)');
			$dbo->query();
			// Fixed :)
		}	
	}

	
	
	/**
	 * Get all table foreign keys
	 *
	 * @param string $tableName Table name
	 */
	protected function _getFKNames($tableName)
	{
		$dbo = JFactory::getDbo();

		$tableName = str_replace('#__', $dbo->getPrefix(), $tableName);
		
		$dbo->setQuery(
			'SELECT CONSTRAINT_NAME FROM information_schema.table_constraints '.
			'WHERE table_schema = SCHEMA() '.
			'AND table_name = "'.$tableName.'" AND '.
			'constraint_type = "FOREIGN KEY"');
		$assoc =  $dbo->loadAssocList();
		
		$res = array(); foreach($assoc as $row) {$res[] = $row['CONSTRAINT_NAME'];}
		
		return $res;
	}

	
	
	/**
	 * Remove foreign keys by name
	 *
	 * @param string $tableName Table name
	 */
	protected function _removeFKs($tableName, $fkNames)
	{
		$dbo = JFactory::getDbo();
		
		$tableName = str_replace('#__', $dbo->getPrefix(), $tableName);

		$fkNames = (array)$fkNames;
		
		foreach ($fkNames as $fkName) {
			$dbo->setQuery('ALTER TABLE '.$tableName.' DROP FOREIGN KEY '.$fkName.'');
			$dbo->query();
		}	
	}
	

	
	/**
	 * Get last schema used by Joomla.
	 * 
	 * @return type 
	 */
	protected function _getLastUsedSchema()
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery(
			'SELECT s.* '.
			'FROM #__schemas AS s '.
			'JOIN #__extensions AS e ON e.extension_id=s.extension_id '.
			'WHERE e.element = "com_newsletter"');
		return $dbo->loadAssoc();
	}
	
	
	
	protected function _getComponentDependentExtensions()
	{
		$installer  = $this->parent;
		
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
			  ->from('#__extensions')
			  ->where(
				  '(type="plugin" AND folder="migur") '.
				  'OR element="mod_newsletter_subscribe" '.
				  'OR (type="plugin" AND element="migurusersync") ');
		 	
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	}
}

