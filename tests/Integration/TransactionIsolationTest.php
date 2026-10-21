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
namespace CakeDC\DbTest\Test\Integration;

use Cake\TestSuite\TestCase;
use CakeDC\DbTest\TestSuite\Fixture\Extension\TestConnectionTrait;

/**
 * Proves that data written in setUp() does not leak into the next test.
 */
class TransactionIsolationTest extends TestCase
{
    use TestConnectionTrait;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->testConnection()->execute('INSERT INTO txn_probe (id) VALUES (NULL)');
    }

    /**
     * @return void
     */
    public function testFirstTestSeesItsOwnRow(): void
    {
        $this->assertSame(1, $this->countRows(), 'setUp() data must be visible inside the test');
    }

    /**
     * @return void
     */
    public function testSecondTestDoesNotSeeThePreviousRow(): void
    {
        $this->assertSame(1, $this->countRows(), 'setUp() data must not survive the previous test');
    }

    /**
     * @return int
     */
    protected function countRows(): int
    {
        return (int)$this->testConnection()
            ->execute('SELECT COUNT(*) FROM txn_probe')
            ->fetchColumn(0);
    }
}
