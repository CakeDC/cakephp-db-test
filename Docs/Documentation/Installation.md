Installation
============

Composer
--------

```
composer require cakedc/cakephp-db-test
```

Load the plugin
---------------

```
bin/cake plugin load CakeDC/DbTest
```
Note you'll need this plugin loaded in the `cli` section of your `Application::bootstrap`, around the line loading Bake Plugin: `$this->addPlugin('Bake');`

Configuration
-------------

Plugin requires an additional database connection to create the database snapshot named **test_template**. 
This connection is used as an unchangeable source of test fixtures.

Add the next configuration setting into app.php


```php
'Datasources' => [
    // ...
    'test_template' => [
            'className' => 'Cake\Database\Connection',
        	'driver' => 'Cake\Database\Driver\Mysql',
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

PHPUnit
-------
Copy https://github.com/CakeDC/cakephp-db-test/blob/master/phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed)

Fixture database
----------------

Note from now on, you will NOT use fixture files, but rely on a "fixture database" allowing you to run migrations to it, modify your fixture data with your sql editor, or import fixtures from the live database using a regular table or database import tool, for example `mysqldump`.


