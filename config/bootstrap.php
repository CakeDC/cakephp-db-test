<?php
use Cake\Core\Configure;
use Cake\Core\Plugin;

Configure::write('DbTest.supportedDrivers', [
    'Mysql' => 'DbTest\\Engine\\MysqlEngine',
    'Postgres' => 'DbTest\\Engine\\PostgresEngine'
]);


