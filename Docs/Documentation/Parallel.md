Parallel (ParaTest)
==================

DbTest runs unchanged under plain `phpunit`. Parallel runs via
[brianium/paratest](https://github.com/paratestphp/paratest) are opt-in and
require one database pair per worker, because every worker restores and wraps
its own `test` database in a transaction.

Requirements
------------

* `brianium/paratest` (require-dev) and one `test` + `test_template` database
  pair per worker, e.g. `myapp_test_1`/`myapp_test_template_1` … `myapp_test_4`
  for `paratest -p4`. ParaTest sets `TEST_TOKEN` (`1`…`N`) in each worker.
* Resolve the worker database names in the host bootstrap from `TEST_TOKEN`
  (see below); DbTest itself only isolates its dump cache file per worker.

Host bootstrap sketch
---------------------

```php
$token = getenv('TEST_TOKEN');
$suffix = $token === false || $token === '' ? '' : '_' . $token;
ConnectionManager::setConfig('test', ['database' => 'myapp_test' . $suffix, /* ... */]);
ConnectionManager::setConfig('test_template', ['database' => 'myapp_test_template' . $suffix, /* ... */]);
```

Dump cache isolation
--------------------

`FixtureManager` caches the skeleton dump at `CACHE/fixtures/db_dump_backup.custom`.
With `TEST_TOKEN` set it uses `db_dump_backup_<TOKEN>.custom` instead, so
workers never export/import over each other. An explicit `cacheSuffix`
extension parameter wins over the environment token. Without a token the
historical path is used, so single-process runs are unaffected.

Extension parameters (`phpunit.xml`)
------------------------------------

```xml
<bootstrap class="CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitExtension">
    <parameter name="verbose" value="0" />
    <!-- all optional, defaults shown -->
    <parameter name="testConnection" value="test" />
    <parameter name="templateConnection" value="test_template" />
    <parameter name="cacheSuffix" value="" />
</bootstrap>
```

`cacheSuffix` takes precedence when set; an empty value forces the shared
historical dump path even under ParaTest (only useful when workers share one
database, which DbTest does not support then keep the default).
