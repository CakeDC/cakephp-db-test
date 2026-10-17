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
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;

/**
 * Opens the per-test transaction before setUp() runs.
 *
 * PHPUnit emits PreparationStarted before the before-test hooks, so opening the
 * transaction here keeps everything written in setUp() (fixtures, factories, save())
 * inside the per-test transaction. The previous BeforeTestMethodCalled event is
 * emitted after setUp(), which committed that data and leaked it into the following
 * tests of the same class.
 */
class PHPUnitPreparationStartedSubscriber implements PreparationStartedSubscriber
{
    /**
     * @param \PHPUnit\Event\Test\PreparationStarted $event Event
     * @return void
     */
    public function notify(PreparationStarted $event): void
    {
        $connection = ConnectionManager::get('test');
        assert($connection instanceof Connection);
        // a previous test may have left its transaction open (e.g. unmet requirements)
        if ($connection->inTransaction()) {
            $connection->rollback(true);
        }
        $connection->begin();
    }
}
