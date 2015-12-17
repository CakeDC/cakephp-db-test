<?php
namespace DbTest\TestSuite\Fixture;

use Cake\Filesystem\Folder;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use DbTest\TestSuite\Fixture\FixtureManager;
use Exception;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;

class FixtureInjector implements PHPUnit_Framework_TestListener {

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
     */
    public function __construct(FixtureManager $manager) {
        $this->_fixtureManager = $manager;
    }

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
     * Called when a test is risky.
     *
     * @param PHPUnit_Framework_Test $test
     * @param Exception              $e
     * @param float                  $time
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
    }

    /**
     * Called at the beginning of a test (per test method in a class)
     *
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test) {
        $ds = ConnectionManager::get('test');
        $ds->begin();
    }

    /**
     * Called when a test ends (per test method in a class)
     *
     * @param PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time) {
        $ds = ConnectionManager::get('test');
        $ds->rollback();
    }

    /**
     * Called at the beginning of a suite. A suite is a collection of tests
     *
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
        Configure::load('app', 'default', false);
        $database = Configure::read('Datasources.test');
        if (!empty($database) && get_class($suite) == 'PHPUnit_Framework_TestSuite') {

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
    protected function _aliasConnections() {
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
    protected function _initDb() {
        if ($this->_initialized) {
            return;
        }
        $this->_aliasConnections();
        $this->_initialized = true;
    }

}

