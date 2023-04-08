<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2017, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\Engine;

use CakeDC\DbTest\Engine\Traits\ExecuteTrait;

abstract class BaseEngine
{
    use ExecuteTrait;

    /**
     * Show commands and results on execution
     *
     * @var bool
     */
    protected $_verbose = false;

    /**
     * Database configuration
     *
     * @var bool
     */
    protected array $_database = [];

    /**
     * Constructor method
     *
     * @param $database
     * @param bool $verbose Show commands and results on execution
     * @return void
     */
    public function __construct($database, $verbose = false)
    {
        $this->_database = $database;
        $this->_verbose = $verbose;
    }

    /**
     * Recreates test database.
     *
     * @return bool
     */
    abstract public function recreateTestDatabase();

    /**
     * Import test skeleton database.
     *
     * @param string $file Sql file path.
     * @param array $options Additional options.
     * @return bool
     */
    abstract public function import($file, $options = []);

    /**
     * Export database.
     *
     * @param string $file Sql file path.
     * @param array $options Additional options.
     * @return bool
     */
    abstract public function export($file, $options = []);

    /**
     * Check if success.
     *
     * @param int $check Check value
     * @return bool
     */
    public function isSuccess($check)
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
    public function createSchema()
    {
        return true;
    }
}
