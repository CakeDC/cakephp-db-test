<?php
App::uses('DbTestListener', 'DbTest.Lib/TestSuite/Listener');
App::uses('DbTestRunner', 'DbTest.Lib/TestSuite/Runner');
App::uses('ConnectionManager', 'Model');

/**
 * A custom test runner.
 * No longer uses fixtures. It relies on the database being built correctly.
 *
 */
class DbTestRunner extends CakeTestRunner {

/**
 * Actually run a suite of tests. This method skips over the CakeTestRunner::doRun
 * and calls the PHPUnit_TextUI_TestRunner::doRun method directly
 * since we no longer want to construct fixtures, etc.
 *
 * @param PHPUnit_Framework_Test $suite
 * @param array                  $arguments
 * @return void
 */
	public function doRun(PHPUnit_Framework_Test $suite, array $arguments = array()) {
		if (isset($arguments['printer'])) {
			self::$versionStringPrinted = true;
		}

		$phpUnitTestRunner = new PHPUnit_TextUI_TestRunner($this->getLoader());
		$arguments['listeners'] = array(new DbTestListener());
		$return = $phpUnitTestRunner::doRun($suite, $arguments);

		return $return;
	}

}