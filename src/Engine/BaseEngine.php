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
use InvalidArgumentException;
use function Cake\I18n\__d;

abstract class BaseEngine implements EngineInterface
{
    use ExecuteTrait;

    /**
     * Show commands and results on execution
     *
     * @var bool
     */
    protected bool $verbose = false;

    /**
     * Database configuration
     *
     * @var array
     */
    protected array $database = [];

    /**
     * Constructor method
     *
     * @param array $database database configuration
     * @param bool $verbose Show commands and results on execution
     */
    public function __construct(array $database, bool $verbose = false)
    {
        if (empty($database['database']) || !is_string($database['database'])) {
            throw new InvalidArgumentException(
                __d('cake_d_c/db_test', 'The datasource configuration must name a `database`'),
            );
        }
        $this->database = $database;
        $this->verbose = $verbose;
    }

    /**
     * Check if success.
     *
     * @param int|null $check Exit status of the executed command, null when unknown
     * @return bool
     */
    public function isSuccess(?int $check): bool
    {
        $allowed = [
            0 => true,
            1 => true,
        ];

        return $check !== null && isset($allowed[$check]);
    }

    /**
     * @inheritDoc
     */
    public function createSchema(): bool
    {
        return true;
    }
}
