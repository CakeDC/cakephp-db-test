Workflow
=======

Adding new fixture
------------------

1. Using any database modification tool (phpmysql, navicat, mysql) modify templates database.
2. Execute `bin/cake fixture_import dump` this will create `config/sql/test_db.sql`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Update database structure
-------------------------

To update database structure the following steps need to beperformed:

1. Apply migrations for test_template database `bin/cake migrations migrate --connection test_template`
2. Execute `bin/cake fixture_import dump`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Running Tests
-------------------
Copy phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed) and then run `phpunit`.
