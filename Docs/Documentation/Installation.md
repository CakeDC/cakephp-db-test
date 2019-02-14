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
            "type": "vcs",
            "url": "git@git.cakedc.com:cakedc/db_test.git"
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

Plugin requires additional database connection to database snapshot, that named template and uses as unchangeable source
of test fixtures.

Add next configuration setting into app/Config/database.php

```php
public $test_template = array(
	'datasource' => 'Database/Mysql',
	'persistent' => false,
	'host' => 'localhost',
	'login' => 'username',
	'password' => 'password',
	'database' => 'template_database_name',
	'prefix' => '',
	'encoding' => 'utf8',
);
```

Login, password and database need to configure.

