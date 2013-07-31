<?php

/**
 * Test class for JLog.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Log
 * @since       11.1
 */
class NewsletterMailerDocumentPlainTest extends TestCaseDatabaseMysql
{
//	public function setUp()
//	{
//		parent::setUp();
//		class_exists('JSession');
//		JFactory::$session = $this->getMock('JSession', array('get'), array(), '', false);
//	}
	
	public function testRenderPlainPlaceholders()
	{
//		JLoader::import('migur.library.mailer');
//		JLoader::import('joomla.library.mailer');
//		JLoader::import('helpers.log', JPATH_COMPONENT_ADMINISTRATOR);
//		
//		$mailer = new NewsletterClassMailer();

//		class_exists('NewsletterClassMailerSender');
//		$sender = $this->getMock('NewsletterClassMailerSender', array('send'))
//			->expects($this->once())
//			->method('send')
//			->will($this->returnCallback(array($this, '_NewsletterClassMailerSender_send_return')));
//		
//		TestReflection::setValue($mailer, '_transport', $sender);
		
		JFactory::$application = TestMockApplication::create($this);
		TestReflection::setValue(JFactory::$application, '_name', 'administrator');
		
		require_once JPATH_LIBRARIES . '/migur/library/mailer/document.php';
		
		
		JLoader::import('helpers.placeholder', JPATH_COMPONENT_ADMINISTRATOR);
		NewsletterHelperPlaceholder::setPlaceholders(array(
			'username'            => 'red',
			'useremail'           => 'orange',
			'sitename'            => 'yelow',
			'subscription key'    => 'green',
			'image_top.alt'		  => 'cayan',
			'table_background'    => 'blue',
			'list name'           => 'magenta'
		));
		
		
		$document = NewsletterClassMailerDocument::factory('plain', array(
			'useRawUrls' => true,
			'tracking'   => true,
			'template' => (object) array(
				'content' => '[username]-[useremail]-[sitename]-[subscription key]-[image_top.alt]-[table_background]-[list name]'
		)));
		
		$this->assertEquals(
			'red-orange-yelow-green-cayan-blue-magenta', 
			$document->render()
		);
	}
	
	public function _NewsletterClassMailerSender_send_return()
	{
		echo func_get_args();
	}
}
