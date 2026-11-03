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
use CakeDC\DbTest\Command\DbTestCommand;
use PHPUnit\Framework\TestCase;

/**
 * DbTestCommandTest
 */
class DbTestCommandTest extends TestCase
{
    /**
     * Without -i the command prints its help and touches no database.
     *
     * @return void
     */
    public function testRunWithoutOptionsPrintsHelp(): void
    {
        $out = new StubConsoleOutput();
        $io = new ConsoleIo($out, new StubConsoleOutput());

        $code = (new DbTestCommand())->run([], $io);

        $this->assertSame(DbTestCommand::CODE_SUCCESS, $code);
        $output = implode("\n", $out->messages());
        $this->assertStringContainsString('Db Test Shell', $output);
        $this->assertStringContainsString('--import-database-template', $output);
        $this->assertStringContainsString('--import-database-file', $output);
        $this->assertStringContainsString('config/sql', $output);
        $this->assertStringNotContainsString('app/Config', $output);
    }
}
