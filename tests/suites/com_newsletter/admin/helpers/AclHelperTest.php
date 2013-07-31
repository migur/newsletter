<?php

/**
 * Test class for JLog.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Log
 * @since       11.1
 */

class AclHelperTest extends TestCase
{
	/**
	 * @var  JTableLanguage
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();

		// Get the mocks
		$this->saveFactoryState();


		JFactory::$session = $this->getMockSession(array(
			'get.user.id' => 1,
			'get.user.name' => 'Name',
			'get.user.username' => 'User Name',
			'get.user.guest' => false
		));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();
	}

	
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable(JTEST_DATABASE_PREFIX.'users', dirname(__FILE__) . '/stubs/#__users.csv');

		return $dataSet;
	}
	
	public function testGetActions()
	{
		JLoader::import('helpers.acl', JPATH_COMPONENT_ADMINISTRATOR);
		$actions = NewsletterHelperAcl::getActions();
		
		$this->assertTrue(in_array('core.admin', $actions));
		$this->assertTrue(in_array('core.manage', $actions));
		$this->assertTrue(in_array('com_newsletter.newsletter.create', $actions));
		$this->assertTrue(in_array('com_newsletter.newsletter.edit', $actions));
		$this->assertTrue(in_array('com_newsletter.list.create', $actions));
		$this->assertTrue(in_array('com_newsletter.list.edit', $actions));
	}
	
	public function testToArray()
	{
		
		// Set mock to return TRUE on any authorise
		$mockJuser = $this->getMock('JUser', array('authorise'));
		$mockJuser
			->expects($this->any())
			->method('authorise')
            ->will($this->returnValue(true));

		JLoader::import('helpers.acl', JPATH_COMPONENT_ADMINISTRATOR);
		
		$array = NewsletterHelperAcl::toArray($mockJuser);
		$this->assertTrue($array['core.admin'], true);
		$this->assertTrue($array['core.manage'], true);
		$this->assertTrue($array['com_newsletter.newsletter.create'], true);
		$this->assertTrue($array['com_newsletter.newsletter.edit'], true);
		$this->assertTrue($array['com_newsletter.list.create'], true);
		$this->assertTrue($array['com_newsletter.list.edit'], true);
	}
}
