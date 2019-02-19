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

use Cake\Network\Exception\NotFoundException;
use Cake\Core\Configure;

class EngineFactory
{

    /**
     * Creates new engine instance.
     *
     * @param array $database Database configuration.
     * @return BaseEngine
     */
    public static function engine($database)
    {
        if (empty($database['driver'])) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Driver is not defined'));
        }
        $type = str_replace('Cake\\Database\\Driver\\', '', $database['driver']);
        $engineType = Configure::read('DbTest.supportedDrivers.' . $type);
        if (empty($engineType)) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Database engine is not supported'));
        }
        if (!class_exists($engineType)) {
            throw new NotFoundException(__d('cake_d_c/db_test', 'Can\'t load engine ' . $engineType));
        }

        return new $engineType($database);
    }
}
