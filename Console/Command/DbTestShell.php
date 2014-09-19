<?php

App::uses('TestShell', 'Console/Command');
App::uses('DbTestListener', 'DbTest.Lib/TestSuite/Listener');
App::uses('DbTestSuiteCommand', 'DbTest.Lib/TestSuite/Command');

class DbTestShell extends TestShell {

	public function getOptionParser() {
		$parser = parent::getOptionParser();
		$parser->description(array(
			__d('cake_console', 'The Db Test Shell extends the CakePhp TestSuite and no longer needs fixtures defined.
			Instead the test and test-template databases are synchronized before each test class is executed.
			Transaction wrapping used to rollback test case changes.'),
		))->addOption('import-database-template', array(
			'boolean' => true,
			'short' => 'i',
			'help' => __d('cake_console', 'Drops test template database and imports test_db.sql file from app/Config/sql'),
		))->addOption('import-database-file', array(
			'help' => __d('cake_console', 'Provides path to test_db.sql file'),
		));
		return $parser;
	}

/**
 * Main entry point for the shell
 */
	public function main() {
		include_once APP . 'Config' . DS . 'database.php';
		if (class_exists('DATABASE_CONFIG')) {
			$config = new DATABASE_CONFIG();
			Configure::write('db.database.test', $config->test);
			Configure::write('db.database.test_template', $config->test_template);
		}

		$this->out(__d('cake_console', 'Db Test Shell'));
		$this->hr();

		$args = $this->_parseArgs();

		if ($this->params['import-database-template']) {
			$this->__importTestSkeleton();
			unset($this->params['import-database-template']);
		} else if (empty($args['case'])) {
			return $this->available();
		}

		if (!empty($args['case'])) {
			$this->_run($args, $this->_runnerOptions());
		}
	}

/**
 * Overrides the CakeTest _run so we can setup our own TestSuiteCommand and TestRunner
 *
 * @param array $runnerArgs
 * @param array $options
 */
	protected function _run($runnerArgs, $options = array()) {
		restore_error_handler();
		restore_error_handler(); 
		$testCli = new DbTestSuiteCommand('CakeTestLoader', $runnerArgs);
		$testCli->run($options);
	}

	private function __importTestSkeleton() {
		$path = null;
		if (!empty($this->params['import-database-file']) && file_exists($this->params['import-database-file'])) {
			$path = $this->params['import-database-file'];
			unset($this->params['import-database-file']);
		}	
		$skeletonDatabase = Configure::read('db.database.test_template');
		$testListener = new DbTestListener();
		$testListener->setupDatabase($skeletonDatabase, true, $path);
	}

}
