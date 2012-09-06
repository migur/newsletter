Requirements:
- Joomla 2.5.6+
- Joomla platform's test folder
- Component's sources



The folders structure:

project
|
+-joomla
|
+-joomlatests
| |	
| +-tests
|   |
|	+-core
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