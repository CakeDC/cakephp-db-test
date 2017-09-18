<?php

namespace DbTest\Shell;

use Cake\Core\Configure;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use DbTest\TestSuite\Fixture\FixtureManager;

class DbTestShell extends Shell {

    /**
     * Parses options from command line
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->description([
            __d('cake_console', 'The Db Test Shell extends the CakePhp TestSuite and no longer needs fixtures defined.
			Instead the test and test-template databases are synchronized before each test class is executed.
			Transaction wrapping used to rollback test case changes.'),
        ])
               ->addOption('import-database-template', [
                   'boolean' => true,
                   'short' => 'i',
                   'help' => __d('cake_console', 'Drops test template database and imports test_db.sql file from app/Config/sql'),
               ])
               ->addOption('import-database-file', [
                   'help' => __d('cake_console', 'Provides path to test_db.sql file'),
               ]);
        return $parser;
    }

    /**
     * Main entry point for the shell
     *
     * @return mixed
     */
    public function main() {
        Configure::load('app', 'default', false);

        $this->out(__d('cake_console', 'Db Test Shell'));
        $this->hr();

        if ($this->params['import-database-template']) {
            $this->__importTestSkeleton();
            unset($this->params['import-database-template']);
        }
    }

    /**
     * Import test skeleton with given database file or default
     *
     * @return void
     */
    private function __importTestSkeleton() {
        $path = null;
        if (!empty($this->params['import-database-file']) && file_exists($this->params['import-database-file'])) {
            $path = $this->params['import-database-file'];
            unset($this->params['import-database-file']);
        }
        $skeletonDatabase = ConnectionManager::get('test_template')->config();
        $manager = new FixtureManager();
        $manager->setupDatabase($skeletonDatabase, true, true, $path);
    }
}
