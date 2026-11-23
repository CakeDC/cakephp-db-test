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
namespace CakeDC\DbTest\Test\TestCase\TestSuite\Parallel;

use CakeDC\DbTest\TestSuite\Parallel\WorkerId;
use PHPUnit\Framework\TestCase;

/**
 * WorkerIdTest
 */
class WorkerIdTest extends TestCase
{
    /**
     * @var string|false
     */
    protected string|false $originalToken = false;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalToken = getenv('TEST_TOKEN');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if ($this->originalToken === false) {
            putenv('TEST_TOKEN');
        } else {
            putenv('TEST_TOKEN=' . $this->originalToken);
        }
        parent::tearDown();
    }

    /**
     * No token: neutral values, single-process paths unchanged.
     *
     * @return void
     */
    public function testNoTokenReturnsNeutralValues(): void
    {
        putenv('TEST_TOKEN');

        $this->assertNull(WorkerId::token());
        $this->assertSame('', WorkerId::suffix());
        $this->assertSame('mydb', WorkerId::suffixed('mydb'));
    }

    /**
     * Empty token behaves like no token.
     *
     * @return void
     */
    public function testEmptyTokenReturnsNeutralValues(): void
    {
        putenv('TEST_TOKEN=');

        $this->assertNull(WorkerId::token());
        $this->assertSame('', WorkerId::suffix());
        $this->assertSame('mydb', WorkerId::suffixed('mydb'));
    }

    /**
     * Token set: suffix and suffixed names carry the worker id.
     *
     * @return void
     */
    public function testTokenReturnsSuffixedValues(): void
    {
        putenv('TEST_TOKEN=3');

        $this->assertSame('3', WorkerId::token());
        $this->assertSame('_3', WorkerId::suffix());
        $this->assertSame('mydb_3', WorkerId::suffixed('mydb'));
    }
}
