<?php
/**
 * Copyright 2010 - 2019, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2017, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\Shell;

use CakeDC\DbTest\Engine\EngineFactory;
use CakeDC\DbTest\TestSuite\Fixture\FixtureInjector;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;
use Cake\Log\Log;

class FixtureImportShell extends Shell
{

    /**
     * Import fixture shell main method
     *
     * @return void
     */
    public function main()
    {
        $this->out($this->getOptionParser()->help());
    }

    /**
     * Get & configure the option parser
     *
     * @return $this
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser->setDescription(__d('cake_d_c/db_test', 'DbTest fixture importer:'))
            ->addOption('dump-folder', [
                'help' => __d('cake_d_c/db_test', 'Provides path to dump test_db.sql file.'),
            ])->addSubcommand('dump', ['help' => __d('cake_d_c/db_test', 'Dumps the template database')]);
    }

    /**
     * Creates fresh dump of templates schema
     *
     * @return void
     */
    public function dump()
    {
        $skeletonDatabase = ConnectionManager::get('test_template')->config();

        if (!empty($skeletonDatabase)) {
            $skeletonName = $skeletonDatabase['database'];

            $dumpFolder = Hash::get($this->params, 'dump-folder', CONFIG . DS . 'sql');
            $this->_ensureFolder($dumpFolder);
            $dumpFile = $dumpFolder . DS . 'test_db.sql';

            $this->out(__d('cake_d_c/db_test', "Exporting data from skeleton database: $skeletonName \n"));
            $engine = EngineFactory::engine($skeletonDatabase);
            $engine->export($dumpFile, ['format' => 'plain']);
        }
    }

    /**
     * Initializes this class with a DataSource object to use as default for all fixtures
     *
     * @return void
     */
    protected function _initDb()
    {
        if ($this->_initialized) {
            return;
        }
        $db = ConnectionManager::get('test_template');
        $db->cacheSources = false;
        $this->_db = $db;
    }

    /**
     * Find and import test_skel.sql file from app/Config/sql
     *
     * @param string $path path
     * @return void
     */
    protected function _ensureFolder($path)
    {
        $Folder = new Folder($path, true);
    }
}
