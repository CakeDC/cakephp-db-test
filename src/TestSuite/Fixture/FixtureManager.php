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
namespace CakeDC\DbTest\TestSuite\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use CakeDC\DbTest\Engine\EngineFactory;
use function Cake\I18n\__d;

class FixtureManager
{
    /**
     * Show commands and results on execution
     *
     * @var bool
     */
    protected bool $verbose = false;

    /**
     * Drops existing connections to test database, recreates db,
     * and transfers data from test_skel to test
     *
     * @param array $database Database configuration
     * @param bool $createSchema true when schema will be created
     * @param bool $importTestSkeleton true when skeleton will be imported
     * @param string|null $sqlFilePath Path to the sql script to import
     * @return bool
     */
    public function setupDatabase(
        array $database,
        bool $createSchema,
        bool $importTestSkeleton = false,
        ?string $sqlFilePath = null,
    ): bool {
        $engine = EngineFactory::engine($database, $this->verbose);

        $success = $engine->recreateTestDatabase();
        if ($success && $createSchema) {
            $success = $engine->createSchema();
        }
        if ($success && $importTestSkeleton) {
            $this->importTestSkeleton($database, $sqlFilePath);
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
     * @param array $database Database configuration
     * @return void
     */
    public function transferData(array $database): void
    {
        $engine = EngineFactory::engine($database, $this->verbose);
        $testDbName = $database['database'];
        $skeletonDatabase = ConnectionManager::get('test_template')->config();
        if (!empty($skeletonDatabase)) {
            $skeletonEngine = EngineFactory::engine($skeletonDatabase, $this->verbose);
            $skeletonName = $skeletonDatabase['database'];

            $cacheFolder = CACHE . 'fixtures';
            $this->ensureFolder($cacheFolder);
            $tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';

            if (!file_exists($tmpFile)) {
                Log::info(__d('cake_d_c/db_test', "Backing up data from skeleton database: $skeletonName \n"));
                $skeletonEngine->export($tmpFile);
            }

            Log::info(__d('cake_d_c/db_test', "Restoring data to: $testDbName \n"));
            $engine->import($tmpFile);
        }
    }

    /**
     * Find and import test_db.sql file from app/Config/sql
     *
     * @param array $database Database configuration.
     * @param string|null $sqlFilePath Path to the sql script to import
     * @return void
     */
    private function importTestSkeleton(array $database, ?string $sqlFilePath = null): void
    {
        $engine = EngineFactory::engine($database, $this->verbose);
        $testDbName = $database['database'];
        $cacheFolder = CACHE . 'fixtures';
        $this->ensureFolder($cacheFolder);
        $tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';
        Log::info(__d('cake_d_c/db_test', "Deleting cached file: $tmpFile \n"));
        if (is_file($tmpFile)) {
            unlink($tmpFile);
        }

        if (empty($sqlFilePath)) {
            $testSkeletonFile = CONFIG . 'sql' . DS . 'test_db.sql';
        } else {
            $testSkeletonFile = $sqlFilePath;
        }

        Log::info(__d('cake_d_c/db_test', "Importing test skeleton from: $testSkeletonFile \n"));
        $engine->import($testSkeletonFile, ['format' => 'plain']);
        Log::info(__d('cake_d_c/db_test', "Backing up data from skeleton database: $testDbName \n\n"));
        $engine->export($tmpFile);
    }

    /**
     * Set verbose mode.
     *
     * @param bool $verbose Verbose
     * @return void
     */
    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    /**
     * Ensure folder exists
     *
     * @param string $path Path to folder
     * @return void
     */
    protected function ensureFolder(string $path): void
    {
        if (!is_dir($path)) {
            mkdir(
                directory: $path,
                permissions: 0755,
                recursive: true,
            );
        }
    }
}
