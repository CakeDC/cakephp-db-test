<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2017, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\TestSuite\Fixture;

use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class FixtureInjector implements TestListener
{

    /**
     * The instance of the fixture manager to use
     *
     * @var \Cake\TestSuite\Fixture\FixtureManager
     */
    protected $_fixtureManager;

    /**
     * Was this class already initialized?
     *
     * @var bool
     */
    protected $_initialized = false;

    /**
     * Constructor. Save internally the reference to the passed fixture manager
     *
     * @param \Cake\TestSuite\Fixture\FixtureManager $manager The fixture manager
     * @return void
     */
    public function __construct(FixtureManager $manager)
    {
        $this->_fixtureManager = $manager;
    }

    private $databaseLoaded = false;

    /**
     * Called when an error is encountered during a test
     *
     * @param Test      $test  Failed Test
     * @param Exception $e     Exception encountered
     * @param float     $time  Time of occurrence
     * @return void
     */
    public function addError(Test $test, Exception $e, $time)
    {
    }

    /**
     * A warning occurred.
     *
     * @param Test    $test
     * @param Warning $e
     * @param float   $time
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
    }

    /**
     * Called when a failure is encountered during a test
     *
     * @param Test                  $test  Failed Test
     * @param AssertionFailedError  $e     Failed Assertion
     * @param float                 $time  Time of occurrence
     * @return void
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
    }

    /**
     * Called if a test is incomplete
     *
     * @param Test      $test  Incomplete Test
     * @param Exception $e     Exception encountered
     * @param float     $time  Time of occurrence
     * @return void
     */
    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * Called when a test is risky.
     *
     * @param Test      $test  Risky Test
     * @param Exception $e     Exception encountered
     * @param float     $time  Time of occurrence
     * @return void
     */
    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * Called when a test is skipped.
     * Tests are skipped when a test it was dependent on fails (using @depends)
     *
     * @param Test      $test  Skipped Test
     * @param Exception $e     Exception encountered
     * @param float     $time  Time of occurrence
     * @return void
     */
    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    /**
     * Called at the beginning of a test (per test method in a class)
     *
     * @param Test  $test  Test
     * @return void
     */
    public function startTest(Test $test)
    {
        ConnectionManager::get('test')->begin();
    }

    /**
     * Called when a test ends (per test method in a class)
     *
     * @param Test  $test  Ended Test
     * @param float $time  Time of occurrence
     * @return void
     */
    public function endTest(Test $test, $time)
    {
        ConnectionManager::get('test')->rollback();
    }

    /**
     * Called at the beginning of a suite. A suite is a collection of tests
     *
     * @param TestSuite $suite    Suite
     * @return void
     */
    public function startTestSuite(TestSuite $suite)
    {
        Configure::load('app', 'default', false);
        $database = ConnectionManager::get('test')->config();
        if (!empty($database) && get_class($suite) == 'PHPUnit\Framework\TestSuite') {
            try {
                $ds = ConnectionManager::get('test');
            } catch (Exception $e) {
                // create test database and schema
                $this->_fixtureManager->setupDatabase($database, true, false);
                $ds = ConnectionManager::get('test');
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
     * @param TestSuite $suite    Suite
     * @return void
     */
    public function endTestSuite(TestSuite $suite)
    {
    }

    /**
     * Disconnects from test database (if necessary), sets up, and reconnects.
     * Disable datasource caching and sets the datasource for the test run.
     *
     * @param string $ds        Directory Separator
     * @param array $database   Database configuration
     * @return void
     */
    private function __loadDatabase($ds, $database)
    {
        if ($ds->isConnected()) {
            // attempt to disconnect and close connection to db.
            $ds->disconnect();
            $ds->close();
        }

        if ($this->_fixtureManager->setupDatabase($database, false)) {
            $this->_fixtureManager->transferData($database);
            $this->databaseLoaded = true;
        }

        if (!$ds->isConnected()) {
            // reconnect
            $ds->disconnect();
            $ds->connect();
        }

        $ds->cacheSources = false;

        $this->_initDb();
    }

    /**
     * Add aliases for all non test prefixed connections.
     *
     * This allows models to use the test connections without
     * a pile of configuration work.
     *
     * @return void
     */
    protected function _aliasConnections()
    {
        $connections = ConnectionManager::configured();
        ConnectionManager::alias('test', 'default');
        $map = [];
        foreach ($connections as $connection) {
            if ($connection === 'test' || $connection === 'default') {
                continue;
            }
            if (isset($map[$connection])) {
                continue;
            }
            if (strpos($connection, 'test_') === 0) {
                $map[$connection] = substr($connection, 5);
            } else {
                $map['test_' . $connection] = $connection;
            }
        }
        foreach ($map as $alias => $connection) {
            ConnectionManager::alias($alias, $connection);
        }
    }

    /**
     * Initializes this class with a DataSource object to use as default for all fixtures
     *
     * @return void
     */
    protected function _initDb()
    {
        if ($this->_initialized) {
            return;
        }
        $this->_aliasConnections();
        $this->_initialized = true;
    }
}
