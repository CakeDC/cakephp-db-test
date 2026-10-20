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
use PHPUnit\Event\TestRunner\Finished;
use PHPUnit\Event\TestRunner\FinishedSubscriber;

/**
 * Final safety net: closes a transaction still open when the run ends.
 *
 * Covers tests that never reached their after-test hooks (unmet requirements,
 * preparation errors) and prevents Connection::__destruct from logging an
 * active transaction.
 */
class PHPUnitTestRunnerFinishedSubscriber implements FinishedSubscriber
{
    /**
     * @param \PHPUnit\Event\TestRunner\Finished $event Event
     * @return void
     */
    public function notify(Finished $event): void
    {
        $connection = ConnectionManager::get('test');
        assert($connection instanceof Connection);
        if ($connection->inTransaction()) {
            $connection->rollback(true);
        }
    }
}
