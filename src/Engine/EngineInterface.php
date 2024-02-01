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
namespace CakeDC\DbTest\Engine;

interface EngineInterface
{
    /**
     * Recreates test database.
     *
     * @return bool
     */
    public function recreateTestDatabase(): bool;

    /**
     * Import test skeleton database.
     *
     * @param string $file Sql file path.
     * @param array $options Additional options.
     * @return bool
     */
    public function import(string $file, array $options = []): bool;

    /**
     * Export database.
     *
     * @param string $file Sql file path.
     * @param array $options Additional options.
     * @return bool
     */
    public function export(string $file, array $options = []): bool;

    /**
     * Create database schema
     *
     * @return bool
     */
    public function createSchema(): bool;
}
