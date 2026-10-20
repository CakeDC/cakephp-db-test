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
namespace CakeDC\DbTest\Test\TestSuite\Fixture;

use CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitPreparationStartedSubscriber;
use CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitTestFinishedSubscriber;
use CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitTestRunnerFinishedSubscriber;
use CakeDC\DbTest\TestSuite\Fixture\Extension\TestConnectionTrait;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * Test-only extension for the SQLite integration suite.
 *
 * Registers the transaction subscribers but not PHPUnitStartedSubscriber,
 * because the latter requires the MySQL/Postgres template database.
 */
class SqliteExtension implements Extension
{
    use TestConnectionTrait;

    /**
     * @param \PHPUnit\TextUI\Configuration\Configuration $configuration Configuration
     * @param \PHPUnit\Runner\Extension\Facade $facade Facade
     * @param \PHPUnit\Runner\Extension\ParameterCollection $parameters Parameters
     * @return void
     */
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $connection = $this->testConnection();
        $connection->execute('DROP TABLE IF EXISTS txn_probe');
        $connection->execute('CREATE TABLE txn_probe (id INTEGER PRIMARY KEY AUTOINCREMENT)');

        $facade->registerSubscribers(
            new PHPUnitPreparationStartedSubscriber(),
            new PHPUnitTestFinishedSubscriber(),
            new PHPUnitTestRunnerFinishedSubscriber()
        );
    }
}
