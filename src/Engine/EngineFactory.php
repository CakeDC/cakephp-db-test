<?php

namespace DbTest\Engine;

use Cake\Network\Exception\NotFoundException;

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
            throw new NotFoundException(__('Driver is not defined'));
        }
        $type = str_replace('Cake\\Database\\Driver\\', '', $database['driver']);
        $supported = [
            'Mysql',
            'Postgres'
        ];
        if (!in_array($type, $supported)) {
            throw new NotFoundException(__('Database engine is not supported'));
        }
        $namespace = 'DbTest\\Engine\\';
        $engineType = $namespace . $type . 'Engine';
        if (!class_exists($engineType)) {
            throw new NotFoundException(__('Can\'t load engine ' . $engineType));
        }

        return new $engineType();
    }
}
