<?php
declare(strict_types=1);

/**
 * Copyright 2013 - 2026, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2026, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\Test\TestSuite\Fixture;

use CakeDC\DbTest\Engine\PostgresEngine;

/**
 * Exposes the protected argument builder and password export for the unit tests.
 */
class PostgresEngineProbe extends PostgresEngine
{
    /**
     * @return string
     */
    public function baseArguments(): string
    {
        return $this->getBaseArguments();
    }

    /**
     * @return void
     */
    public function exposePassword(): void
    {
        $this->setPassword();
    }
}
