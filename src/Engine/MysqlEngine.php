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
namespace CakeDC\DbTest\Engine;

use Cake\Filesystem\File;
use Cake\Log\Log;

class MysqlEngine extends BaseEngine
{

    /**
     * Recreates test database.
     *
     * @param array $database Database configuration.
     * @return bool
     */
    public function recreateTestDatabase()
    {
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
        $output = [];
        $success = 0;
        Log::info(__d('cake_d_c/db_test', "Dropping database: $databaseName \n"));
        $this->_execute("mysqladmin -f $baseArgs drop $databaseName", $output, $success);
        if ($this->isSuccess($success)) {
            Log::info(__d('cake_d_c/db_test', "Creating database: $databaseName \n"));
            $this->_execute("mysqladmin -f $baseArgs create $databaseName", $output, $success);
        }

        return $success === 0;
    }

    /**
     * Import test skeleton database.
     *
     * @param string $path Path to the folder containing all the sql files
     * @param array  $options  Additional options/
     * @return bool
     */
    public function import($path, $options = [])
    {
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
        $files = glob("$path/*.sql");
        if (!$files) {
            throw new \OutOfBoundsException(sprintf('No sql files found in %s', $path));
        }
        $tmpFile = tempnam(TMP, 'dbtest_');
        foreach ($files as $file) {
            file_put_contents($tmpFile, file_get_contents($file), FILE_APPEND);
        }
        $command = "mysql $baseArgs $databaseName < $tmpFile";
        dd($command);
        $result = $this->_execute($command, $output);
        unlink($tmpFile);

        return $result;
    }

    /**
     * Export database.
     *
     * @param string $path base folder to export tables, one per file
     * @param array  $options  Additional options/
     * @return bool
     */
    public function export($path, $options = [])
    {
        if (!$path) {
            throw new \OutOfBoundsException('Base path is required to store all tables, 1 per file');
        }
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();

        $tablesCommand = "mysql -B -s -e 'show tables' $baseArgs $databaseName";
        $tables = $this->_execute($tablesCommand, $tablesOutput);
        if (!$tablesOutput) {
            throw new \OutOfBoundsException('No tables found in database %s', $databaseName);
        }
        foreach ($tablesOutput as $tableName) {
            $command = "mysqldump $baseArgs $databaseName $tableName | grep -v '/*!50013 DEFINER'";
            $command .= " > \"$path/$tableName.sql\"";
            $this->_execute($command, $output);
        }
    }

    /**
     * Format common arguments.
     *
     * @return string
     */
    protected function _getBaseArguments()
    {
        $user = isset($this->_database['username']) ? $this->_database['username'] : null;
        $password = isset($this->_database['password']) ? $this->_database['password'] : null;
        $host = $this->_database['host'];
        $port = '';
        if (!empty($this->_database['port'])) {
            $port = " --port=" . $this->_database['port'];
        }
        $quote = DS === '/' ? "'" : '"';

        return "--host=$host $port --user=$user --password=$quote$password$quote";
    }
}
