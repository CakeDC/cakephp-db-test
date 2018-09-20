Workflow
=======

Adding new fixture
------------------

1. Using any database modification tool (phpmysql, navicat, mysql) modify templates database.
2. Perform `bin/cake fixture_import dump > app/Config/sql/test_db.sql`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Update database structure
-------------------------

To update database structure need perform next steps:

1. Apply migrations for test_template database `bin/cake migrations migrate --connection test_template`
2. Perform `bin/cake fixture_import dump > app/Config/sql/test_db.sql`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Importing legacy fixtures
-------------------------

In case we migrating to DbTest from cakephp testsuite and have fixtures in files
we can import them:

```
cake DbTest.FixtureImport import FixutreName --plugin PluginName
```

By default supposed that tables structures already loaded using migration process.
But one can use key --import-structure to create table.

Store database dump
-------------------

For handy storing dump of template database you can you next shell action.

```
bin/cake fixture_import dump
```
