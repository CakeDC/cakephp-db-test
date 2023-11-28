<?php
declare(strict_types=1);

/**
 * Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;

class DbTestShell extends Shell
{
    /**
     * Parses options from command line
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser->setDescription([
            __d('cake_d_c/db_test', 'The Db Test Shell extends the CakePhp TestSuite and no longer needs fixtures defined.
			Instead the test and test-template databases are synchronized before each test class is executed.
			Transaction wrapping used to rollback test case changes.'),
        ])->addOption('import-database-template', [
            'boolean' => true,
            'short' => 'i',
            'help' => __d('cake_d_c/db_test', 'Drops test template database and imports test_db.sql file from app/Config/sql'),
        ])->addOption('import-database-file', [
            'help' => __d('cake_d_c/db_test', 'Provides path to test_db.sql file'),
        ]);

        return $parser;
    }

    /**
     * Main entry point for the shell
     *
     * @return mixed
     */
    public function main()
    {
        Configure::load('app', 'default', false);

        $this->out(__d('cake_d_c/db_test', 'Db Test Shell'));
        $this->hr();

        if ($this->params['import-database-template']) {
            $this->__importTestSkeleton();
            unset($this->params['import-database-template']);
        } else {
            $this->out($this->getOptionParser()->help());
        }
    }

    /**
     * Import test skeleton with given database file or default
     *
     * @return void
     */
    private function __importTestSkeleton()
    {
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
