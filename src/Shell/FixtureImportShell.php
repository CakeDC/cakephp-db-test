<?php

namespace DbTest\Shell;

use Cake\Core\Configure;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;
use DbTest\Engine\EngineFactory;
use DbTest\TestSuite\Fixture\FixtureInjector;

class FixtureImportShell extends Shell
{

    /**
     * Import fixture shell main method
     *
     * @return void
     */
    public function main()
    {
        $this->_initDb();
        // $this->import();
    }

    /**
     * Get & configure the option parser
     *
     * @return void
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser->description(__('DbTest fixture importer:'))
                      ->addOption('import-structure', [
                          'boolean' => true,
                          'default' => false,
                          'help' => __('Creates table structure in template schema. By default imports data only.')
                      ])
                      ->addOption('plugin', [
                          'help' => __('Load fixture from the plugin folder.')
                      ])
                    ->addOption('dump-folder', [
                        'help' => __d('cake_console', 'Provides path to dump test_db.sql file.'),
                    ])
                      ->addOption('fixture', [
                          'help' => __('Comma separated list of directories to exclude.')
                      ]);
    }

    /**
     * Creates fresh dump of templates schema
     *
     * @return void
     */
    public function dump()
    {
        Configure::load('app', 'default', false);
        $skeletonDatabase = ConnectionManager::get('test_template')->config();

        if (!empty($skeletonDatabase)) {
            $skeletonName = $skeletonDatabase['database'];

            $dumpFolder = Hash::get($this->params, 'dump-folder', CONFIG . DS . 'sql');
            $this->_ensureFolder($dumpFolder);
            $dumpFile = $dumpFolder . DS . 'test_db.sql';

            print "Exporting data from skeleton database: $skeletonName \n";
            $engine = EngineFactory::engine($skeletonDatabase);
            $engine->export($skeletonDatabase, $dumpFile, ['format' => 'plain']);
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
