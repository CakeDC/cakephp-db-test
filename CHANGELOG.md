Changelog
=========

Releases for CakePHP 5.0
------------------------
* 3.1.0
    * **Breaking (behaviour)** — the per-test transaction now opens before `setUp()` runs, so data written in `setUp()` (fixtures, factories, `save()`) is rolled back with the test instead of being committed. Tests that relied on `setUp()` data carrying over to later tests in the same class (for example, seeding once behind a static flag) must seed per test.
    * Note — do not combine this extension with tests that declare `$fixtures`: CakePHP's `TransactionStrategy` requires no active transaction, and the per-test transaction is already open when `setUp()` runs. Use one strategy or the other.
    * Fix: the transaction is opened before `setUp()` (`PreparationStarted`), so `setUp()` data no longer leaks into the next test
    * Fix: the per-test transaction is rolled back with `rollback(true)` consistently and closed on `Test\Finished` / `TestRunner\Finished`, including tests that never reach their after-hooks
    * New: SQLite integration suite (`composer test-integration`) proving `setUp()` data does not survive between tests
    * Chore: `TransactionGuard` centralises begin/end, and the unit tests no longer build PHPUnit internals
    * CI: PHP 8.1–8.5 matrix, coding standard job, integration suite, and no duplicate runs on PR branches

Previous releases (3.0.x and earlier) predate this changelog. See the Git history and tags.
