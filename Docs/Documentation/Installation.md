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

Configuration
-------------

Plugin requires an additional database connection to create the database snapshot named **test_template**. 
This connection is used as an unchangeable source of test fixtures.

Add the next configuration setting into app.php


```php
'Datasources' => [
    // ...
    'test_template' => [
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
Copy phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed)

