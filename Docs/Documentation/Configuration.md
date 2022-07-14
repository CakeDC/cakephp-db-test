Configuration
=============

**DbTest** Config options:

* `DbTest.dumpNoTablespaces` (?bool, default null). Will append `--no-tablespaces` when doing a mysqldump (in MysqlEngine).
* `DbTest.dumpExtendedInserts` (?bool, default null). IF `false` it will append `--extended-insert=FALSE` when doing a mysqldump (in MysqlEngine).