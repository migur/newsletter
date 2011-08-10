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
		}
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

			if (strpos($row[0], self::$comNamespace) !== false) {

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