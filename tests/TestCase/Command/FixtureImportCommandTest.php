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
namespace CakeDC\DbTest\Test\TestCase\Command;

use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\Datasource\ConnectionManager;
use CakeDC\DbTest\Command\FixtureImportCommand;
use PHPUnit\Framework\TestCase;

/**
 * FixtureImportCommandTest
 */
class FixtureImportCommandTest extends TestCase
{
    /**
     * @return void
     */
    protected function tearDown(): void
    {
        ConnectionManager::drop('test_template');
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testMissingTemplateDatasourceIsReportedAsError(): void
    {
        $this->assertNotContains('test_template', ConnectionManager::configured());
        $out = new StubConsoleOutput();
        $err = new StubConsoleOutput();
        $io = new ConsoleIo($out, $err);

        $code = (new FixtureImportCommand())->run([], $io);

        $this->assertSame(FixtureImportCommand::CODE_ERROR, $code);
        $this->assertStringContainsString('Missing `test_template` datasource configuration', implode("\n", $err->messages()));
    }

    /**
     * @return void
     */
    public function testUnsupportedDriverIsRejectedBeforeAnyDump(): void
    {
        ConnectionManager::setConfig('test_template', ['url' => 'sqlite:///:memory:']);
        $io = new ConsoleIo(new StubConsoleOutput(), new StubConsoleOutput());

        $this->expectExceptionMessage('Database engine Sqlite is not supported');

        (new FixtureImportCommand())->run([], $io);
    }
}
