Overview
========

The **DbTest** plugin enables developers to speeding up tests running on mysql database.

The logic of DbTest different from testsuite in the way it handles fixtures.

The CakePHP testsuite uses fixtures to initialize test database, DbTest don't use fixtures at all.
Instead it uses an additional template database, that is initialized based on the app/Config/db_test.sql file.

Beneric cycle of test execution under DbTest.
--------------------------------------------------------------

* When test suite started with DbTest it, loads test database from template database snapshot.
* Before test method started DbTest initialize transaction.
* After test method finished DbTest rollback transaction.

This way database modifications are quickly restored, but it makes it a requirement that the table has to use a transaction engine like InnoDb.

Support
-----------

For bugs and feature requests, please use the [issues](https://git.cakedc.com/cakedc/db_test/issues) section of this repository.

Commercial support is also available, [contact us](http://cakedc.com/contact) for more information.

Contributing
------------

If you'd like to contribute new features, enhancements or bug fixes to the plugin, just read our [Contribution Guidelines](http://cakedc.com/plugins) for detailed instructions.

License
-------

Copyright 2007-2014 Cake Development Corporation (CakeDC). All rights reserved.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.
