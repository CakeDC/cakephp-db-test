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
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

/**
 * Closes the per-test transaction when the test finished.
 *
 * AfterTestMethodFinished is not emitted for test classes without after-test
 * hooks; Finished is emitted for every prepared test, so it covers that gap.
 */
class PHPUnitTestFinishedSubscriber implements FinishedSubscriber
{
    /**
     * @param \PHPUnit\Event\Test\Finished $event Event
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
