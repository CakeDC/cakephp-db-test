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

use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * PHPUnitExtension
 */
class PHPUnitExtension implements Extension
{
    /**
     * @param \PHPUnit\TextUI\Configuration\Configuration $configuration Configuration
     * @param \PHPUnit\Runner\Extension\Facade $facade Facade
     * @param \PHPUnit\Runner\Extension\ParameterCollection $parameters Parameters
     * @return void
     */
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $fixtureManager = new FixtureManager();
        $verbose = (bool)$parameters->get('verbose');
        $fixtureManager->setVerbose($verbose);

        $facade->registerSubscribers(
            new PHPUnitStartedSubscriber($fixtureManager),
            new PHPUnitBeforeTestMethodCalledSubscriber(),
            new PHPUnitAfterTestMethodFinishedSubscriber()
        );
    }
}
