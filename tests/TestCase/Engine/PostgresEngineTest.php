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

use CakeDC\DbTest\Test\TestSuite\Fixture\PostgresEngineProbe;
use PHPUnit\Framework\TestCase;

/**
 * PostgresEngineTest
 */
class PostgresEngineTest extends TestCase
{
    /**
     * @var string|false
     */
    protected string|false $ambientPassword = false;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ambientPassword = getenv('PGPASSWORD');
        putenv('PGPASSWORD');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->ambientPassword === false) {
            putenv('PGPASSWORD');
        } else {
            putenv('PGPASSWORD=' . $this->ambientPassword);
        }
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testBaseArgumentsWithFullConfig(): void
    {
        $engine = new PostgresEngineProbe([
            'database' => 'app_test',
            'host' => 'db',
            'port' => 5433,
            'username' => 'user',
            'password' => 'secret',
        ]);

        $this->assertSame('--host=db --port=5433 --username=user', $engine->baseArguments());
        $engine->exposePassword();
        $this->assertSame('secret', getenv('PGPASSWORD'));
    }

    /**
     * @return void
     */
    public function testBaseArgumentsWithoutOptionalKeys(): void
    {
        $engine = new PostgresEngineProbe(['database' => 'app_test']);

        $this->assertSame('', $engine->baseArguments());
        $engine->exposePassword();
        $this->assertFalse(getenv('PGPASSWORD'));
    }
}
