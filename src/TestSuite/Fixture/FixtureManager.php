<?php

namespace DbTest\TestSuite\Fixture;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use DbTest\Engine\EngineFactory;

class FixtureManager
{

    /**
     * Drops existing connections to test database, recreates db,
     * and transfers data from test_skel to test
     *
     * @param array  $database Database configuration
     * @param bool   $createSchema true when schema will be created
     * @param bool   $importTestSkeleton true when skeleton will be imported
     * @param string $sqlFilePath Path to the sql script to import
     * @return bool
     */
    public function setupDatabase($database, $createSchema, $importTestSkeleton = false, $sqlFilePath = null)
    {
        $engine = EngineFactory::engine($database);

        $success = $engine->recreateTestDatabase();
        if ($success && $createSchema) {
            $success = $engine->createSchema();
        }
        if ($success && $importTestSkeleton) {
            $this->__importTestSkeleton($database, $sqlFilePath);
        }

        return $success;
    }

    /**
     * Transfers data from test_skel database to test database.
     * Caches initial back up to increase the speed of future transfers.
     * This cached file is stored in the core's fixture cache
     *
     * mysqlamdin must be in your path and be the proper version for your database
     *
     * @param string $database Database configuration
     * @return void
     */
    public function transferData($database)
    {
        $testDbName = $database['database'];
        $skeletonDatabase = ConnectionManager::get('test_template')->config();
        if (!empty($skeletonDatabase)) {
            $skeletonName = $skeletonDatabase['database'];

            $cacheFolder = CACHE . 'fixtures';
            $this->_ensureFolder($cacheFolder);
            $tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';

            if (!file_exists($tmpFile)) {
                print "Backing up data from skeleton database: $skeletonName \n";
                $engine = EngineFactory::engine($skeletonDatabase);
                $engine->export($tmpFile);
            }

            print "Restoring data to: $testDbName \n";
            $engine = EngineFactory::engine($database);
            $engine->import($tmpFile);
        }
    }

    /**
     * Find and import test_db.sql file from app/Config/sql
     *
     * @param string $database Database configuration.
     * @param string $sqlFilePath Path to the sql script to import
     * @return void
     */
    private function __importTestSkeleton($database, $sqlFilePath = null)
    {
        $testDbName = $database['database'];
        $cacheFolder = CACHE . 'fixtures';
        $this->_ensureFolder($cacheFolder);
        $tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';
        print "Deleting cached file: $tmpFile \n";
        if (is_file($tmpFile)) {
            unlink($tmpFile);
        }

        if (empty($sqlFilePath)) {
            $testSkeletonFile = CONFIG . 'sql' . DS . 'test_db.sql';
        } else {
            $testSkeletonFile = $sqlFilePath;
        }

        $engine = EngineFactory::engine($database);
        print "Importing test skeleton from: $testSkeletonFile \n";
        $engine->import($testSkeletonFile, ['format' => 'plain']);
        print "Backing up data from skeleton database: $testDbName \n\n";
        $engine->export($tmpFile);
    }

    /**
     * Ensure folder exists
     *
     * @param string $path Path to folder
     * @return void
     */
    protected function _ensureFolder($path)
    {
        $Folder = new Folder($path, true);
    }
}
