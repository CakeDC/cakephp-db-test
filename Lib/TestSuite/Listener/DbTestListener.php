<?php
require_once 'PHPUnit/Framework/TestListener.php';

App::uses('Folder', 'Utility');
App::uses('EngineFactory', 'DbTest.Lib/Engine');
class DbTestListener implements PHPUnit_Framework_TestListener {

	private $databaseLoaded = false;

/**
 * Called when an error is encountered during a test
 *
 * @param PHPUnit_Framework_Test $test
 * @param Exception              $e
 * @param float                  $time
 */
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
	}

/**
 * Called when a failure is encountered during a test
 *
 * @param PHPUnit_Framework_Test                 $test
 * @param PHPUnit_Framework_AssertionFailedError $e
 * @param float                                  $time
 */
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
	}

/**
 * Called if a test is incomplete
 *
 * @param PHPUnit_Framework_Test $test
 * @param Exception              $e
 * @param float                  $time
 */
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
	}

/**
 * Called when a test is skipped.
 * Tests are skipped when a test it was dependent on fails (using @depends)
 *
 * @param PHPUnit_Framework_Test $test
 * @param Exception              $e
 * @param float                  $time
 */
	public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
	}

	/**
	 * Called at the beginning of a test (per test method in a class)
	 *
	 * @param PHPUnit_Framework_Test $test
	 */
	public function startTest(PHPUnit_Framework_Test $test) {
		$ds = ConnectionManager::getDataSource('test');
		$ds->begin();
	}

/**
 * Called when a test ends (per test method in a class)
 *
 * @param PHPUnit_Framework_Test $test
 * @param float                  $time
 */
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		$ds = ConnectionManager::getDataSource('test');
		$ds->rollback();
	}

/**
 * Called at the beginning of a suite. A suite is a collection of tests
 *
 * @param PHPUnit_Framework_TestSuite $suite
 */
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		$database = Configure::read('db.database.test');
		if (!empty($database) && get_class($suite) == 'PHPUnit_Framework_TestSuite') {

			try{
				$ds = ConnectionManager::getDataSource('test');
			} catch (Exception $e) {
				// create test database and schema
				$this->setupDatabase($database, true, false);
				$ds = ConnectionManager::getDataSource('test');
			}

			// rollback transaction. gives us a 'fresh' copy of the database for the next test suite to run
			$ds->rollback();

			if (!$this->databaseLoaded) {
				$this->__loadDatabase($ds, $database);
			}
			// begin transaction. will hold all changes while the test suite runs.
		}
	}

/**
 * Called at the end of a suite.
 *
 * @param PHPUnit_Framework_TestSuite $suite
 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
	}

/**
 * Disconnects from test database (if necessary), sets up, and reconnects.
 * Disable datasource caching and sets the datasource for the test run.
 *
 * @param string $ds
 * @param string $database
 */
	private function __loadDatabase($ds, $database) {
		if ($ds->isConnected()) {
			// attempt to disconnect and close connection to db.
			$ds->disconnect();
			$ds->close();
		}

		if ($this->setupDatabase($database, false)) {
			$this->__transferData($database);
			$this->databaseLoaded = true;
		}

		if (!$ds->isConnected()) {
			// reconnect
			$ds->reconnect();
		}

		$ds->cacheSources = false;

		// this is required since if no fixtures are present,
		// CakePhp for some reason runs against the 'default' database
		ClassRegistry::config(array('ds' => 'test', 'testing' => true));
	}

/**
 * Drops existing connections to test database, recreates db,
 * and transfers data from test_skel to test
 *
 * @param array $database
 * @param bool $createSchema
 * @param bool $importTestSkeleton
 * @param string $sqlFilePath
 * @return bool
 */
	public function setupDatabase($database, $createSchema, $importTestSkeleton = false, $sqlFilePath = null) {
		$engine = EngineFactory::engine($database);

		$success = $engine->recreateTestDatabase($database);
		if ($success && $createSchema) {
			$success = $engine->createSchema($database);
		}
		if ($success && $importTestSkeleton) {
			$this->__importTestSkeleton($database, $sqlFilePath);
		}

		return $success;
	}

/**
 * Transfers data from test_skel database to test database.
 * Caches initial back up to increase the speed of future transfers.
 * This cached file is stored in the core's fixture cache
 *
 * pg_dump and pg_restore must be in your path and be the proper version for your database
 *
 * @param string $database
 */
	private function __transferData($database) {
		$testDbName = $database['database'];
		$skeletonDatabase = Configure::read('db.database.test_template');
		if (!empty($skeletonDatabase)) {
			$skeletonName = $skeletonDatabase['database'];

			$cacheFolder = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'cache' . DS . 'fixtures';
			$this->_ensureFolder($cacheFolder);
			$tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';

			if (!file_exists($tmpFile)) {
				print "Backing up data from skeleton database: $skeletonName \n";
				$engine = EngineFactory::engine($skeletonDatabase);
				$engine->export($skeletonDatabase, $tmpFile);
			}

			print "Restoring data to: $testDbName \n";
			$engine = EngineFactory::engine($database);
			$engine->import($database, $tmpFile);
		}
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $database
 * @param string $sqlFilePath
 */
	private function __importTestSkeleton($database, $sqlFilePath = null) {
		$testDbName = $database['database'];
		$cacheFolder = ROOT . DS . APP_DIR . DS . 'tmp' . DS . 'cache' . DS . 'fixtures';
		$this->_ensureFolder($cacheFolder);
		$tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';
		print "Deleting cached file: $tmpFile \n";
		if (is_file($tmpFile)) {
			unlink($tmpFile);
		}

		if (empty($sqlFilePath)) {
			$testSkeletonFile = ROOT . DS . APP_DIR . DS . 'Config' . DS . 'sql' . DS . 'test_db.sql';
		} else {
			$testSkeletonFile = $sqlFilePath;
		}

		$engine = EngineFactory::engine($database);
		print "Importing test skeleton from: $testSkeletonFile \n";
		$engine->import($database, $testSkeletonFile, array('format' => 'plain'));
		print "Backing up data from skeleton database: $testDbName \n\n";
		$engine->export($database, $tmpFile);
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $path
 */
	protected function _ensureFolder($path) {
		$Folder = new Folder($path, true);
	}
}

