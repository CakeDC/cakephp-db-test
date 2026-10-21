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
namespace CakeDC\DbTest\Test\TestCase\TestSuite\Fixture\Extension;

use Cake\Database\Connection;
use CakeDC\DbTest\TestSuite\Fixture\Extension\TransactionGuard;
use PHPUnit\Framework\TestCase;

/**
 * TransactionGuardTest
 */
class TransactionGuardTest extends TestCase
{
    /**
     * @var \Cake\Database\Connection
     */
    protected Connection $connection;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->fakeConnection();
    }

    /**
     * @return void
     */
    public function testBeginOpensTransaction(): void
    {
        (new TransactionGuard())->begin($this->connection);

        $this->assertSame(['begin'], $this->connection->calls);
        $this->assertTrue($this->connection->inTransaction);
    }

    /**
     * @return void
     */
    public function testBeginRollsBackAbandonedTransactionFirst(): void
    {
        $this->connection->inTransaction = true;

        (new TransactionGuard())->begin($this->connection);

        $this->assertSame(['rollback', 'begin'], $this->connection->calls);
        $this->assertTrue($this->connection->inTransaction);
    }

    /**
     * @return void
     */
    public function testEndRollsBackOpenTransaction(): void
    {
        $this->connection->inTransaction = true;

        (new TransactionGuard())->end($this->connection);

        $this->assertSame(['rollback'], $this->connection->calls);
        $this->assertFalse($this->connection->inTransaction);
    }

    /**
     * @return void
     */
    public function testEndDoesNothingWithoutTransaction(): void
    {
        (new TransactionGuard())->end($this->connection);

        $this->assertSame([], $this->connection->calls);
    }

    /**
     * @return \Cake\Database\Connection
     */
    protected function fakeConnection(): Connection
    {
        return new class extends Connection {
            /**
             * @var array<string>
             */
            public array $calls = [];

            /**
             * @var bool
             */
            public bool $inTransaction = false;

            /**
             * Skip the parent constructor, the test never touches the driver.
             */
            public function __construct()
            {
            }

            /**
             * @return bool
             */
            public function inTransaction(): bool
            {
                return $this->inTransaction;
            }

            /**
             * @return void
             */
            public function begin(): void
            {
                $this->calls[] = 'begin';
                $this->inTransaction = true;
            }

            /**
             * @param bool|null $toBeginning Whether the transaction should be rolled back to the beginning.
             * @return bool
             */
            public function rollback(?bool $toBeginning = null): bool
            {
                $this->calls[] = 'rollback';
                $this->inTransaction = false;

                return true;
            }
        };
    }
}
