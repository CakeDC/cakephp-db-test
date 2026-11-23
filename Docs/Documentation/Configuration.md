Configuration
=============

**DbTest** Config options:

* `DbTest.dumpNoTablespaces` (?bool, default null). Will append `--no-tablespaces` when doing a mysqldump (in MysqlEngine).
* `DbTest.dumpExtendedInserts` (?bool, default null). IF `false` it will append `--extended-insert=FALSE` when doing a mysqldump (in MysqlEngine).

Extension parameters (`phpunit.xml`, all optional):

* `testConnection` (string, default `test`). Connection holding the test database.
* `templateConnection` (string, default `test_template`). Connection holding the template database.
* `cacheSuffix` (string, default auto from `TEST_TOKEN`). Dump cache file suffix; empty forces the shared historical path.

See [Parallel (ParaTest)](Parallel.md) for the parallel setup.