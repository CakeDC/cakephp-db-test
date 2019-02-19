Installation
============

Composer
--------

```
composer require cakedc/db-test
```

Configuration
-------------

Plugin requires an additional database connection to create the database snapshot named **test_template**. 
This connection is used as an unchangeable source of test fixtures.

Add the next configuration setting into app.php


```php
'Datasources' => [
    test_template => [
        public $test_template = [
        	'driver' => 'Cake\Database\Driver\Mysql',
        	'persistent' => false,
        	'host' => 'localhost',
        	'username' => 'username',
        	'password' => 'password',
        	'database' => 'template_database_name',
        	'prefix' => '',
        	'encoding' => 'utf8',
        ];
    ]
]
```

Load the plugin in your bootstrap.php
```php
Plugin::load('CakeDC/DbTest', ['bootstrap' => true]);
```

PHPUnit
-------------
Copy phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed)

