Changelog
=========

Releases for CakePHP 6.0
------------------------
* 4.0.0 (unreleased, tracks `cakephp/cakephp` `6.x-dev` until 6.0.0 is tagged; verified against core commit `421c644`, PHPUnit 13.4.6, PHP 8.5.12)
    * **Breaking (requirements)** — PHP 8.5+, CakePHP 6.0 (`^6.0`, resolves `6.x-dev`: applications need `minimum-stability: dev` and `prefer-stable: true`), PHPUnit 13.
    * **Breaking (API)** — the protected members of `BaseEngine`, `MysqlEngine`, `PostgresEngine`, `ExecuteTrait` and `FixtureManager` lost their underscore prefix, following CakePHP 6 and the `cakephp/cakephp-codesniffer` 6 standard: `_execute()` -> `execute()`, `_getBaseArguments()` -> `getBaseArguments()`, `_setPassword()` -> `setPassword()`, `__importTestSkeleton()` -> `importTestSkeleton()`, `_ensureFolder()` -> `ensureFolder()`, `$_verbose` -> `$verbose`, `$_database` -> `$database`. Subclasses must rename their overrides.
    * **Breaking (API)** — `BaseEngine::isSuccess()` accepts `?int` (a `null` exit status is a failure).
    * Fix: the `verbose` extension parameter is optional and parsed as a boolean string (`"false"` used to enable verbose logging, a missing parameter threw `ParameterDoesNotExistException`); `PHPUnitExtension::isVerbose()` is public
    * Fix: engines build the CLI arguments from the keys the datasource defines (`host`, `port`, `username`, `password`) instead of raising `Undefined array key`; `PGPASSWORD` is only exported when a password is configured; a datasource without `database` throws `InvalidArgumentException` at engine construction
    * Fix: `db_test` help text names `config/sql`; docs: verbose mode and plugin loading examples updated
    * Fix: `fixture_import` reports a missing `test_template` datasource with its error message instead of an uncaught `MissingDatasourceConfigException`
    * Fix: the MySQL password is shell-escaped (`escapeshellarg()`); a password containing a quote no longer breaks the command
    * `DbTestCommand` and `FixtureImportCommand` follow the CakePHP 6 command shape: parameterless `execute()` reading `$this->args` and `$this->io`.
    * Note — name `host`, `username`, `password` and `database` on the `test` and `test_template` connections (and `schema` on PostgreSQL when `createSchema()` should run): the engines read the raw datasource array (`Connection::config()`), the driver defaults (`localhost`, the default port, `public`) are never applied to it.
    * Chore: `phpunit.xml.dbtest` migrated to the PHPUnit 13 schema (the `apc.enable_cli` ini line is gone: it is `PHP_INI_SYSTEM` and PHPUnit 12.5+ warns on it).
    * Chore: the plugin's own suites fail on deprecations, notices and warnings; PHPStan 2 level 8 over `src/` and `tests/` without a baseline (only `missingType.iterableValue` is ignored by identifier) replaces the phive-installed PHPStan 1.10 and Psalm 5.10; `composer stan` runs it, `composer check` runs cs-check, stan and the unit suite.
    * CI: PHP 8.5, coding standard and PHPStan job, `4.next-cake6` branch.

Releases for CakePHP 5.0
------------------------
* 3.1.0
    * **Breaking (behaviour)** — the per-test transaction now opens before `setUp()` runs, so data written in `setUp()` (fixtures, factories, `save()`) is rolled back with the test instead of being committed. Tests that relied on `setUp()` data carrying over to later tests in the same class (for example, seeding once behind a static flag) must seed per test.
    * **Breaking (API)** — `PHPUnitBeforeTestMethodCalledSubscriber` was removed; register `PHPUnitPreparationStartedSubscriber` instead. Applications that registered the old subscriber by hand will get a fatal error after upgrading.
    * Note — do not combine this extension with tests that declare `$fixtures`: CakePHP's `TransactionStrategy` requires no active transaction, and the per-test transaction is already open when `setUp()` runs. Use one strategy or the other.
    * Fix: the transaction is opened before `setUp()` (`PreparationStarted`), so `setUp()` data no longer leaks into the next test
    * Fix: the per-test transaction is rolled back with `rollback(true)` consistently. Prepared tests are closed on `Test\Finished`; tests that never reach it (unmet requirements, or a skip/error during `setUp()`) are closed by the next test's `PreparationStarted` or by `TestRunner\Finished` at the end of the run. In that case a `tearDownAfterClass()` that runs in between is rolled back with the still-open transaction
    * New: SQLite integration suite (`composer test-integration`) proving `setUp()` data does not survive between tests
    * New public API: `PHPUnitPreparationStartedSubscriber`, `PHPUnitTestFinishedSubscriber`, `PHPUnitTestRunnerFinishedSubscriber` and `TransactionGuard`, plus the `TestConnectionTrait` helper; `PHPUnitAfterTestMethodFinishedSubscriber` is kept for backwards compatibility
    * Chore: `TransactionGuard` centralises begin/end, and the unit tests no longer build PHPUnit internals
    * CI: PHP 8.1–8.5 matrix, coding standard job, integration suite, and no duplicate runs on PR branches

Previous releases (3.0.x and earlier) predate this changelog. See the Git history and tags.
