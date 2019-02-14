Examples
========

Import template database
------------------------

Importing template database form sql file, by default it will load `config/sql/test_db.sql`

```
bin/cake db_test -i
```

Use `--import-database-file` to specify the sql file to be loaded

```
bin/cake db_test -i --import-database-file=files/dump.sql
```


Running test case
-----------------

Copy phpunit.xml.dbtest as phpunit.xml.dist in your project and then run `vendor/bin/phpunit`.

