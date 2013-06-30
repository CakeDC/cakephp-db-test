<?php

App::uses('CakeTestSuiteCommand', 'TestSuite');
App::uses('DbTestRunner', 'DbTest.Lib/TestSuite/Runner');

class DbTestSuiteCommand extends CakeTestSuiteCommand {

/**
 * Simply extends CakePhp's CakeTestSuiteCommand run method
 * so we can inject our own runner later on
 *
 * @param array $argv
 * @param bool  $exit
 *
 * @return int|void
 */
	public function run(array $argv, $exit = true) {
		parent::run($argv, $exit);
	}

/**
 * Setup and return our test runner
 *
 * @param string $loader
 * @return CakeTestRunner|DbTestRunner
 */
	public function getRunner($loader) {
		return new DbTestRunner($loader, $this->_params);
	}

}