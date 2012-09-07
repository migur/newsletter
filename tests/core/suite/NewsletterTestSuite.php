<?php

class NewsletterTestSuite extends PHPUnit_Framework_TestSuite
{
	public static $connection;
	
    public static function suite()
    {
        $suite = new NewsletterTestSuite('com_newsletter');
		
		$path[] = 'suites';
		$tests = array();
		while(count($path) != 0)
		{
			$v = array_shift($path);
			foreach(glob($v) as $item)
			{
				if (is_dir($item)) 
					$path[] = $item . '/*';
				
				elseif (is_file($item))
					if (preg_match('/.+Test\.php$/', $item)) $tests[] = $item;
			}
		}		
		
		$suite->addTestFiles($tests);
		return $suite;
    }
 
    protected function setUp()
    {
		// Constants
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/constants.php';

		// Autoloader
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/autoload.php';
		NewsletterHelperAutoload::setup();
		
		// Db upgrade
		if (JTEST_DB_MIGRATION) $this->_dbUp();
    }
 
    protected function tearDown()
    {
		if (JTEST_DB_MIGRATION) $this->_dbDown();
    }
	
	protected function _dbUp()
	{
		echo "\nDatabase upgrade...";
		// We always want the default database test case to use an SQLite memory database.
		
		$options = array(
			'driver'     => 'mysql',
			'prefix'     => JTEST_DATABASE_PREFIX
		);
		
		$params = explode(';', JTEST_DATABASE_MYSQL_DSN);
		foreach($params as $item) {
			
			list($key, $val) = explode('=', $item);
			
			if ($key == 'dbname') $key = 'database';
			if ($key == 'pass') $key = 'password';
			
			$options[$key] = $val;
		}
		
		try
		{
			// Attempt to instantiate the driver.
			$driver = JDatabase::getInstance($options);
			
			// Create a new PDO instance for an SQLite memory database and load the test schema into it.
			$dsn = 'mysql:host=' . $options['host'] . ';dbname=' . $options['database'];
			$pdo = new PDO($dsn, $options['user'], $options['password']);
			$ddl = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/install/install.sql');
			$ddl = str_replace('#__', JTEST_DATABASE_PREFIX, $ddl);
			$res = $pdo->exec($ddl);
			
			if ($res !== 0) throw new RuntimeException('Cannot upgrade DB');
			
			// Set the PDO instance to the driver using reflection whizbangery.
			TestReflection::setValue($driver, 'connection', $pdo);
		}
		catch (RuntimeException $e)
		{
			$driver = null;
		}

		// If for some reason an exception object was returned set our database object to null.
		if ($driver instanceof Exception)
		{
			$driver = null;
		}

		// Setup the factory pointer for the driver and stash the old one.
		JFactory::$database = $driver;
		echo "ok";
	}
	
	protected function _dbDown()
	{
		echo "\nDatabase degrade...";

		$pdo = TestReflection::getValue(JFactory::$database, 'connection');
		
		// Set the PDO instance to the driver using reflection whizbangery.
		$ddl = file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . '/install/uninstall.sql');
		$ddl = str_replace('#__', JTEST_DATABASE_PREFIX, $ddl);
		$res = $pdo->exec($ddl);
		if ($res !== 0) throw new RuntimeException('Cannot degrade DB');
		echo "ok";
	}
}
