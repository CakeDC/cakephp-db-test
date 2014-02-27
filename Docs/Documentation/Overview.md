Overview
========

The **DbTest** plugin enables developers to speeding up tests running on mysql database.

The logic of DbTest different from testsuite in way it handling fixtures.

If cakephp testsuite uses fixtures to initialize test database, DbTest dont use fixtures at all.
Instead it uses addational template database, that initialized based on app/Config/db_test.sql file.

Generic cycle of test execution under DbTest.
--------------------------------------------------------------

* When test suite started with DbTest it loads test database from template database snapshot.
* Before test method started DbTest initialize transaction.
* After test method finished DbTest rollback transaction.

This way database modification quikly restored, but it put requirement that table should use transaction engine like InnoDb.

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
