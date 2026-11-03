Installation
============

Composer
--------

```
composer require cakedc/cakephp-db-test
```

Until CakePHP 6.0.0 is tagged the plugin resolves against `cakephp/cakephp` `6.x-dev`, so the application's
`composer.json` needs `"minimum-stability": "dev"` and `"prefer-stable": true`.

Load the plugin
---------------

```
bin/cake plugin load CakeDC/DbTest
```
Or load it from `Application::bootstrap()` next to the other CLI-only plugins (Bake, Migrations):
`$this->addPlugin('CakeDC/DbTest');`

Configuration
-------------

Plugin requires an additional database connection to create the database snapshot named **test_template**.
This connection is used as an unchangeable source of test fixtures.

Add the next configuration setting into app.php


```php
'Datasources' => [
    // ...
    'test_template' => [
            'className' => \Cake\Database\Connection::class,
        	'driver' => \Cake\Database\Driver\Mysql::class,
        	'persistent' => false,
        	'host' => 'localhost',
        	'username' => 'my_app',
        	'password' => 'secret',
        	'database' => 'template_test_myapp',
        	'prefix' => '',
        	'encoding' => 'utf8',
    ],
    // ...
```

Name `host`, `username`, `password` and `database` explicitly on both the `test` and the `test_template`
connections: the plugin reads the raw datasource array (`Connection::config()`), the driver defaults are never
applied to it, and the values go to `mysql`/`mysqladmin`/`mysqldump` or `psql`/`pg_dump`/`pg_restore` on the
command line. On PostgreSQL also name `schema` when the test database needs a `CREATE SCHEMA` after it is
recreated; without the key no schema is created.

PHPUnit
-------
Copy https://github.com/CakeDC/cakephp-db-test/blob/4.next-cake6/phpunit.xml.dbtest as phpunit.xml.dist in your
project (modify if needed)

Fixture database
----------------

Note from now on, you will NOT use fixture files, but rely on a "fixture database" allowing you to run
migrations to it, modify your fixture data with your sql editor, or import fixtures from the live
database using a regular table or database import tool, for example `mysqldump`.


