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

use CakeDC\DbTest\Engine\Traits\ExecuteTrait;

abstract class BaseEngine implements EngineInterface
{
    use ExecuteTrait;

    /**
     * Show commands and results on execution
     *
     * @var bool
     */
    protected bool $_verbose = false;

    /**
     * Database configuration
     *
     * @var array
     */
    protected array $_database = [];

    /**
     * Constructor method
     *
     * @param array $database database configuration
     * @param bool $verbose Show commands and results on execution
     */
    public function __construct(array $database, bool $verbose = false)
    {
        $this->_database = $database;
        $this->_verbose = $verbose;
    }

    /**
     * Check if success.
     *
     * @param int $check Check value
     * @return bool
     */
    public function isSuccess(int $check): bool
    {
        $allowed = [
            0 => true,
            1 => true,
        ];

        return isset($allowed[$check]) && $allowed[$check];
    }

    /**
     * Create schema
     *
     * @return bool
     */
    public function createSchema(): bool
    {
        return true;
    }
}
