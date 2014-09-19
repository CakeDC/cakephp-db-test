Examples
========

Import template database
------------------------

Importing template database form sql file

```
cake DbTest.db_test -i
```


Running test case
-----------------

Execute test case using DbTest.

```
cake DbTest.db_test app TestName
```

Store database dump
-------------------

For handy storing dump of template database you can you next shell action.

```
cake DbTest.FixtureImport dump
```

Importing legacy fixtures
-------------------------

```
cake DbTest.FixtureImport import FixutreName --plugin PluginName
```
