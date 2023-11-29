<?php
declare(strict_types=1);

/**
 * Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\TestSuite\Fixture\Extension;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;
use Exception;
use PHPUnit\Event\TestSuite\Started;
use PHPUnit\Event\TestSuite\StartedSubscriber;

/**
 * PHPUnitStartedSubscriber
 */
class PHPUnitStartedSubscriber implements StartedSubscriber
{
    /**
     * @var true
     */
    protected bool $databaseLoaded;
    /**
     * @var true
     */
    protected bool $initialized;

    /**
     * @param \CakeDC\DbTest\TestSuite\Fixture\FixtureManager $fixtureManager Fixture Manager instance
     */
    public function __construct(
        private readonly FixtureManager $fixtureManager
    ) {
    }

    /**
     * @param \PHPUnit\Event\TestSuite\Started $event Event
     * @return void
     */
    public function notify(Started $event): void
    {
        $connection = ConnectionManager::get('test');
        assert($connection instanceof Connection);
        $database = $connection->config();

        try {
            $ds = ConnectionManager::get('test');
        } catch (Exception) {
            // create test database and schema
            $this->fixtureManager->setupDatabase($database, true, false);
            $ds = ConnectionManager::get('test');
        }
        assert($ds instanceof Connection);
        // rollback transaction. gives us a 'fresh' copy of the database for the next
        // test suite to run
        $ds->rollback();

        if (!$this->databaseLoaded) {
            $this->loadDatabase($ds, $database);
        }
        // begin transaction. will hold all changes while the test suite runs.
    }

    /**
     * Disconnects from test database (if necessary), sets up, and reconnects.
     * Disable datasource caching and sets the datasource for the test run.
     *
     * @param \Cake\Database\Connection $ds Directory Separator
     * @param array $database Database configuration
     * @return void
     */
    protected function loadDatabase(Connection $ds, array $database): void
    {
        if ($ds->getDriver()->isConnected()) {
            // attempt to disconnect and close connection to db.
            $ds->getDriver()->disconnect();
        }

        if ($this->fixtureManager->setupDatabase($database, false)) {
            $this->fixtureManager->transferData($database);
            $this->databaseLoaded = true;
        }

        if (!$ds->getDriver()->isConnected()) {
            // reconnect
            $ds->getDriver()->disconnect();
            $ds->getDriver()->connect();
        }

        $this->initDb();
    }

    /**
     * Add aliases for all non test prefixed connections.
     *
     * This allows models to use the test connections without
     * a pile of configuration work.
     *
     * @return void
     */
    protected function aliasConnections(): void
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
            if (str_starts_with($connection, 'test_')) {
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
    protected function initDb(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->aliasConnections();
        $this->initialized = true;
    }
}
