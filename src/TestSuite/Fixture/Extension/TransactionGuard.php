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

/**
 * Opens and closes the transaction that wraps each test.
 */
class TransactionGuard
{
    /**
     * Opens the per-test transaction, discarding a leftover one first.
     *
     * @param \Cake\Database\Connection $connection Test connection.
     * @return void
     */
    public function begin(Connection $connection): void
    {
        if ($connection->inTransaction()) {
            $connection->rollback(true);
        }

        $connection->begin();
    }

    /**
     * Rolls back the per-test transaction if it is still open.
     *
     * @param \Cake\Database\Connection $connection Test connection.
     * @return void
     */
    public function end(Connection $connection): void
    {
        if ($connection->inTransaction()) {
            $connection->rollback(true);
        }
    }
}
