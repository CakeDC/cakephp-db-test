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
use RuntimeException;

/**
 * Resolves and validates the test connection used by the extension.
 */
trait TestConnectionTrait
{
    /**
     * Connection holding the test database wrapped in the per-test transaction.
     *
     * @var string
     */
    protected string $testConnectionName = 'test';

    /**
     * Set the connection holding the test database.
     *
     * @param string $testConnectionName Connection name.
     * @return void
     */
    public function setTestConnectionName(string $testConnectionName): void
    {
        $this->testConnectionName = $testConnectionName;
    }

    /**
     * @return \Cake\Database\Connection
     * @throws \RuntimeException When the test connection is not a database connection.
     */
    protected function testConnection(): Connection
    {
        return $this->asConnection(ConnectionManager::get($this->testConnectionName));
    }

    /**
     * @param mixed $connection Connection to validate.
     * @return \Cake\Database\Connection
     * @throws \RuntimeException When the connection is not a database connection.
     */
    protected function asConnection(mixed $connection): Connection
    {
        if (!$connection instanceof Connection) {
            throw new RuntimeException(sprintf(
                'The "%s" connection must be a Cake\Database\Connection instance.',
                $this->testConnectionName
            ));
        }

        return $connection;
    }
}
