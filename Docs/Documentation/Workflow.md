Workflow
=======

Adding new fixture
------------------

1. Using any database modification tool (phpmysql, navicat, mysql) modify templates database.
2. Execute `bin/cake fixture_import` this will create `config/sql/test_db.sql`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Update database structure
-------------------------

To update database structure the following steps need to beperformed:

1. Apply migrations for test_template database `bin/cake migrations migrate --connection test_template`
2. Execute `bin/cake fixture_import`
3. Execute `bin/cake db_test -i` to import sql file.
4. Add modified sql file into repository.

Running Tests
-------------------
Copy phpunit.xml.dbtest as phpunit.xml.dist in your project (modify if needed) and then run `phpunit`.

Debug Test Suite
-------------------
Verbose mode logs every shell command the engines run and its output (`Log::info()`). Enable it with the `verbose`
parameter of the extension in `phpunit.xml.dist` (`"true"`, `"1"`, `"on"` or `"yes"`; omitted or `"false"` is off):

```xml
<extensions>
    <bootstrap class="CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitExtension">
        <parameter name="verbose" value="true"/>
    </bootstrap>
</extensions>
```
