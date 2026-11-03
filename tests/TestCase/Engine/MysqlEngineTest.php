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
namespace CakeDC\DbTest\Test\TestCase\Engine;

use CakeDC\DbTest\Engine\MysqlEngine;
use CakeDC\DbTest\Test\TestSuite\Fixture\MysqlEngineProbe;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * MysqlEngineTest
 */
class MysqlEngineTest extends TestCase
{
    /**
     * @return void
     */
    public function testBaseArgumentsWithFullConfig(): void
    {
        $engine = new MysqlEngineProbe([
            'database' => 'app_test',
            'host' => 'db',
            'port' => 3307,
            'username' => 'user',
            'password' => 'secret',
        ]);

        $this->assertSame("--host=db --port=3307 --user=user --password='secret'", $engine->baseArguments());
    }

    /**
     * @return void
     */
    public function testBaseArgumentsWithoutOptionalKeys(): void
    {
        $engine = new MysqlEngineProbe(['database' => 'app_test']);

        $this->assertSame('', $engine->baseArguments());
    }

    /**
     * @return void
     */
    public function testEmptyPasswordIsPassedEmpty(): void
    {
        $engine = new MysqlEngineProbe(['database' => 'app_test', 'username' => 'root', 'password' => '']);

        $this->assertSame("--user=root --password=''", $engine->baseArguments());
    }

    /**
     * @return void
     */
    public function testDatabaseNameIsRequired(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage('must name a `database`');

        new MysqlEngine(['host' => 'db']);
    }
}
