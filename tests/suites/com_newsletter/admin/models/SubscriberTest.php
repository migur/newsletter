<?php

/**
 * Test class for JLog.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Log
 * @since       11.1
 */
class SubscriberTest extends TestCaseDatabaseMysql
{
	public function setUp()
	{
		parent::setUp();
		class_exists('JSession');
		JFactory::$session = $this->getMock('JSession', array('get'), array(), '', false);
	}
	
	/**
	 * @var  JTableLanguage
	 */
	protected $object;

	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable(JTEST_DATABASE_PREFIX.'users', dirname(__FILE__) . '/stubs/#__users.csv');
		$dataSet->addTable(JTEST_DATABASE_PREFIX.'newsletter_subscribers', dirname(__FILE__) . '/stubs/#__newsletter_subscribers.csv');
		return $dataSet;
	}
	
	public function testGetItem()
	{
		$model = new NewsletterModelSubscriber;


		// Get absent subscriber
		$subscriber = $model->getItem(123456);
		$this->assertFalse($subscriber, 'Result on fetch absent item is not FALSE');


		// Get existing free subscriber
		$subscriber = $model->getItem(100);
		// Anyway this is array and it not empty and juser_id absent
		$this->assertTrue(is_array($subscriber));
		$this->assertNotEmpty($subscriber['subscriber_id'], '100:assertNotEmpty subscriber_id');
		$this->assertEmpty($subscriber['juser_id'], '100:assertEmpty juser_id');

		
		// Get existing free subscriber
		$subscriber = $model->getItem(102);
		//This is array and it not empty and juser_id is present
		$this->assertTrue(is_array($subscriber));
		$this->assertNotEmpty($subscriber['subscriber_id']);
		$this->assertNotEmpty($subscriber['juser_id']);
		$this->assertEquals('User Name II', $subscriber['name'], 'Name is not from JUSER');
		$this->assertEquals('user2@blackhole.com', $subscriber['email'], 'Email is not from JUSER');


		// Get existing juser by email
		$subscriber = $model->getItem(array('email' => 'user3@blackhole.com'));
		//This is array and it not empty and juser_id is present
		$this->assertTrue(is_array($subscriber));
		$this->assertNotEmpty($subscriber['subscriber_id']);
		$this->assertNotEmpty($subscriber['juser_id']);
		$this->assertEquals('User Name III', $subscriber['name']);
		$this->assertEquals('user3@blackhole.com', $subscriber['email']);

		// In addition there should be created a new subscriber record
		$query = $this->getConnection()->getConnection()->query('SELECT * FROM '.JTEST_DATABASE_PREFIX.'newsletter_subscribers WHERE user_id = 3');
		$query->execute(); $res = $query->fetchAll();
		// Array with 1 item. This item have non-empty subscriber_id
		$this->assertTrue(is_array($res));
		$this->assertCount(1,$res);
		$this->assertNotEmpty($res[0]['subscriber_id']);
	}
}
