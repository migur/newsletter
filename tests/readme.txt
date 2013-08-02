Requirements:
- Joomla 2.5.6+
- Component's source files


The folders structure:

project
|
+-joomla
|
+-newsletter



phpunit.xml
	database DSN
		<const name="JTEST_DATABASE_MYSQL_DSN" value="host=10.10.1.113;dbname=migur.joomla.unittests;user=p_migur;pass=vbueh" />
	table's prafix	
		<const name="JTEST_DATABASE_PREFIX" value="test_" />
	if needed to use component's DDL to update the DB
		<const name="JTEST_DB_MIGRATION" value="true" />

All the tests:
	suites/com_newsletter

Suite class:
	suites/com_newsletter/NewsletterTestSuite.php

Test listener class:
	TestListener.php

Bootstrap:
	bootstrap.php
	import.php



Flow:
1. Create valid 2.5.6+ joomla DB (migur.joomla.unittests as example).
2. Configure phpunit.xml with DB access and other.
3. Deploy the component over joomla with help of ANT newsletter/build.xml because
component's autoloading, classes' dependencies and files' paths are intended to work inside of joomla site.
4. Go to "newsletter/tests" folder and run PHPUNIT.
5. Do some changes, ANT, PHPUNIT. Changes, ANT, PHPUNIT...
5. Have fun!
