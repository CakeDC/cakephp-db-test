Installation
============

To install the plugin, place the files in a directory labelled "DbTest/" in your "app/Plugin/" directory.

Composer
--------

Add a repository to your composer.json and specify

```
"cakedc/db_test": "^3.0"
```

```
    "repositories": [
        {
            "type": "package",
            "package": {
                "version": "3.0",
                "name": "cakedc/DbTest",
                "type": "cakephp-plugin",
                "source": {
                    "url": "git@git.cakedc.com:cakedc/db_test.git",
                    "type": "git",
                    "reference": "3.0"
                },
                "require": {
                    "php": ">=5.4.16"
                },
                "require-dev": {
                    "phpunit/phpunit": "*"
                },
                "autoload": {
                    "psr-4": {
                        "DbTest\\": "src"
                    }
                },
                "autoload-dev": {
                    "psr-4": {
                        "DbTest\\Test\\": "tests",
                        "Cake\\Test\\": "./vendor/cakephp/cakephp/tests"
                    }
                }
            }
        }
    ],
```

Git Submodule
-------------

If you're using git for version control, you may want to add the **DbTest** plugin as a submodule on your repository. To do so, run the following command from the base of your repository:

```
git submodule add git@git.cakedc.com:cakedc/db_test.git app/Plugin/DbTest
```

After doing so, you will see the submodule in your changes pending, plus the file ".gitmodules". Simply commit and push to your repository.

To initialize the submodule(s) run the following command:

```
git submodule update --init --recursive
```

To retreive the latest updates to the plugin, assuming you're using the "master" branch, go to "app/Plugin/DbTest" and run the following command:

```
git pull origin master
```

If you're using another branch, just change "master" for the branch you are currently using.

If any updates are added, go back to the base of your own repository, commit and push your changes. This will update your repository to point to the latest updates to the plugin.


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
Plugin::load('DbTest', ['bootstrap' => true]);
```
Copy phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed)

