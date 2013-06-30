Plugin provides diferent db fixtures loading. Instead of loading fixtures each time complete test schema loaded from temaplates database. Each test case wrapped in transaction and all changes happened in test case rewerted when it finished.

## Setup ##

1. Define in database.php "test" and "test_template" data sources.

	public $test = array(
		'datasource' => 'Database/Mysql',
		...
		'database' => 'test_db',
	);

	public $test_template = array(
		'datasource' => 'Database/Mysql',
		...
		'database' => 'test_db_template',
	);


2. In app/Config/sql/test_db.sql put sql dump of template database.

3. Create folder app/tmp/cache/fixtures.

4. To load actual template db data perform "cake db_test -i"

5. To run test use same testsuite params in command line but instead of 'testsuite' shell use 'DbTest.db_test'.

