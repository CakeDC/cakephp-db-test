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
namespace CakeDC\DbTest\Test\TestCase\TestSuite\Fixture;

use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;
use PHPUnit\Framework\TestCase;

/**
 * FixtureManagerTest
 */
class FixtureManagerTest extends TestCase
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
     * No token: historical dump path.
     *
     * @return void
     */
    public function testDumpFileWithoutToken(): void
    {
        putenv('TEST_TOKEN');

        $manager = new FixtureManager();

        $this->assertSame(CACHE . 'fixtures' . DS . 'db_dump_backup.custom', $manager->dumpFile());
    }

    /**
     * Token set: per-worker dump path.
     *
     * @return void
     */
    public function testDumpFileWithToken(): void
    {
        putenv('TEST_TOKEN=2');

        $manager = new FixtureManager();

        $this->assertSame(CACHE . 'fixtures' . DS . 'db_dump_backup_2.custom', $manager->dumpFile());
    }

    /**
     * Explicit suffix wins over the environment token.
     *
     * @return void
     */
    public function testDumpFileWithExplicitSuffix(): void
    {
        putenv('TEST_TOKEN=2');

        $manager = new FixtureManager();
        $manager->setCacheSuffix('_manual');

        $this->assertSame(CACHE . 'fixtures' . DS . 'db_dump_backup_manual.custom', $manager->dumpFile());
    }
}
