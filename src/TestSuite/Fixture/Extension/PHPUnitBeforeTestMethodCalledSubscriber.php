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
use PHPUnit\Event\Test\BeforeTestMethodCalled;
use PHPUnit\Event\Test\BeforeTestMethodCalledSubscriber;

/**
 * PHPUnitBeforeTestMethodCalledSubscriber
 */
class PHPUnitBeforeTestMethodCalledSubscriber implements BeforeTestMethodCalledSubscriber
{
    public function notify(BeforeTestMethodCalled $event): void
    {
        ConnectionManager::get('test')->begin();
    }
}
