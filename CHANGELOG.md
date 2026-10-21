Changelog
=========

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
