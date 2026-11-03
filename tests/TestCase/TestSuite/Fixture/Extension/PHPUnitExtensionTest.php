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
namespace CakeDC\DbTest\Test\TestCase\TestSuite\Fixture\Extension;

use CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Extension\ParameterCollection;

/**
 * PHPUnitExtensionTest
 */
class PHPUnitExtensionTest extends TestCase
{
    /**
     * @return array<string, array{array<string, string>, bool}>
     */
    public static function verboseProvider(): array
    {
        return [
            'missing' => [[], false],
            'false' => [['verbose' => 'false'], false],
            'zero' => [['verbose' => '0'], false],
            'empty' => [['verbose' => ''], false],
            'garbage' => [['verbose' => 'maybe'], false],
            'true' => [['verbose' => 'true'], true],
            'one' => [['verbose' => '1'], true],
            'on' => [['verbose' => 'on'], true],
        ];
    }

    /**
     * @param array<string, string> $parameters Extension parameters
     * @param bool $expected Expected result
     * @return void
     */
    #[DataProvider('verboseProvider')]
    public function testIsVerbose(array $parameters, bool $expected): void
    {
        $extension = new PHPUnitExtension();

        $this->assertSame($expected, $extension->isVerbose(ParameterCollection::fromArray($parameters)));
    }
}
