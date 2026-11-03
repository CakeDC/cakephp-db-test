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

use Cake\Database\Connection;

/**
 * Connection double that records begin/rollback calls instead of touching a driver.
 */
class RecordingConnection extends Connection
{
    /**
     * @var array<string>
     */
    public array $calls = [];

    /**
     * @var bool
     */
    public bool $inTransaction = false;

    /**
     * Skip the parent constructor, the tests never touch the driver.
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * @return void
     */
    public function begin(): void
    {
        $this->calls[] = 'begin';
        $this->inTransaction = true;
    }

    /**
     * @param bool|null $toBeginning Whether the transaction should be rolled back to the beginning.
     * @return bool
     */
    public function rollback(?bool $toBeginning = null): bool
    {
        $this->calls[] = 'rollback';
        $this->inTransaction = false;

        return true;
    }
}
