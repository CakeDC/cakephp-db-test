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

use Cake\Datasource\ConnectionManager;
use PHPUnit\Event\Test\AfterTestMethodFinished;
use PHPUnit\Event\Test\AfterTestMethodFinishedSubscriber;

/**
 * PHPUnitAfterTestMethodFinishedSubscriber
 */
class PHPUnitAfterTestMethodFinishedSubscriber implements AfterTestMethodFinishedSubscriber
{
    /**
     * @param \PHPUnit\Event\Test\AfterTestMethodFinished $event Event
     * @return void
     */
    public function notify(AfterTestMethodFinished $event): void
    {
        ConnectionManager::get('test')->rollback();
    }
}
