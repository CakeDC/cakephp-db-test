Examples
========

Import template database
------------------------

Importing template database form sql file, by default it will load config/sql/test_db.sql

```
bin/cake db_test -i
```

With `--import-database-file` you can specify the sql file to be loaded

```
bin/cake db_test -i --import-database-file=files/dump.sql
```


Running test case
-----------------

Copy phpunit.xml.dbtest as phpunit.xml.dist in your project and then run `phpunit`.

Store database dump
-------------------

For handy storing dump of template database you can you next shell action.

```
bin/cake fixture_import dump
```

With `--import-database-file` you can specify the folder to add the sql file

```
bin/cake fixture_import dump --dump-folder=files
```
