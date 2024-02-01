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

use Cake\Http\Exception\NotFoundException;
use function Cake\I18n\__d;

class EngineFactory
{
    /**
     * Creates new engine instance.
     *
     * @param array $database Database configuration.
     * @param bool $verbose Show commands and results on execution
     * @return \CakeDC\DbTest\Engine\EngineInterface
     */
    public static function engine(array $database, bool $verbose = false): EngineInterface
    {
        if (empty($database['driver'])) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Driver is not defined'));
        }
        $engineClass = static::getEngineClass($database['driver']);

        return new $engineClass($database, $verbose);
    }

    /**
     * Translate a cake engine into a DbTest engine
     *
     * @param string $driver Driver name
     * @return string
     */
    protected static function getEngineClass(string $driver): string
    {
        $engineMap = [
            'Mysql' => MysqlEngine::class,
            'Postgres' => PostgresEngine::class,
        ];

        $type = str_replace('Cake\\Database\\Driver\\', '', $driver);
        if (!in_array($type, array_keys($engineMap))) {
            throw new NotFoundException(
                __d('cake_d_c/db_test', 'Database engine {0} is not supported', $type)
            );
        }

        $engineClass = $engineMap[$type];
        if (!class_exists($engineClass)) {
            throw new NotFoundException(
                __d('cake_d_c/db_test', 'Can\'t load engine ' . $engineClass)
            );
        }

        return $engineClass;
    }
}
