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

use Cake\Http\Exception\NotFoundException;

class EngineFactory
{
    /**
     * Creates new engine instance.
     *
     * @param array $database Database configuration.
     * @param bool $verbose Show commands and results on execution
     * @return BaseEngine
     */
    public static function engine($database, $verbose = false)
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
     * @param $driver
     */
    protected static function getEngineClass($driver)
    {
        $engineMap = [
            'Mysql' => '\\CakeDC\\DbTest\\Engine\\MysqlEngine',
            'Postgres' => '\\CakeDC\\DbTest\\Engine\\PostgresEngine'
        ];

        $type = str_replace('Cake\\Database\\Driver\\', '', $driver);
        if (!in_array($type, array_keys($engineMap))) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Database engine {0} is not supported', $type));
        }

        $engineClass = $engineMap[$type];
        if (!class_exists($engineClass)) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Can\'t load engine ' . $engineClass));
        }

        return $engineClass;
    }
}
