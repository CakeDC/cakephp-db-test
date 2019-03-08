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

use Cake\Log\Log;

class PostgresEngine extends BaseEngine
{

    /**
     * Recreates test database.
     *
     * @return bool
     */
    public function recreateTestDatabase()
    {
        $baseArgs = $this->_getBaseArguments();
        $this->_setPassword();
        $databaseName = $this->_database['database'];
        $systemUser = 'postgres';
        $terminateQuery = "select pg_terminate_backend(pg_stat_activity.pid) from pg_stat_activity where pg_stat_activity.datname = '$databaseName'";
        $this->_execute("psql $baseArgs -c \"$terminateQuery\" $systemUser", $output, $success);

        $output = [];
        $success = 0;
        Log::info(__d('cake_d_c/db_test', "Dropping database: $databaseName \n"));
        $this->_execute("dropdb $baseArgs $databaseName", $output, $success);

        if ($this->isSuccess($success)) {
            Log::info(__d('cake_d_c/db_test', "Creating database: $databaseName \n"));
            $this->_execute("createdb $baseArgs $databaseName", $output, $success);
        }

        return $this->isSuccess($success);
    }

    /**
     * Create schema
     *
     * @return bool
     */
    public function createSchema()
    {
        $baseArgs = $this->_getBaseArguments();
        $this->_setPassword();
        $testDbName = $this->_database['database'];
        if (!empty($this->_database['schema'])) {
            $schema = $this->_database['schema'];
        }

        if (!empty($schema)) {
            $this->_execute("psql $baseArgs -c \"create schema $schema;\" $testDbName", $output, $success);
        }

        return $this->isSuccess($success);
    }

    /**
     * Import test skeleton database.
     *
     * @param string $file     Sql file path.
     * @param array  $options  Additional options/
     * @return bool
     */
    public function import($file, $options = [])
    {
        $baseArgs = $this->_getBaseArguments();
        $testDbName = $this->_database['database'];
        $this->_setPassword($this->_database);

        if (isset($options['format']) && $options['format'] == 'plain') {
            $command = "psql $baseArgs $testDbName < $file";
        } else {
            $command = "pg_restore $baseArgs -j 8 -Fc -d $testDbName $file";
        }

        return $this->_execute($command, $output);
    }

    /**
     * Export database.
     *
     * @param string $file     Sql file path.
     * @param array  $options  Additional options/
     * @return bool
     */
    public function export($file, $options = [])
    {
        $baseArgs = $this->_getBaseArguments();
        $this->_setPassword();
        $testDbName = $this->_database['database'];
        $format = ' -Fc ';
        if (isset($options['format']) && $options['format'] == 'plain') {
            $format = " -Fp ";
        }

        $command = "pg_dump $baseArgs  -Z=0 --file=$file $format $testDbName";

        return $this->_execute($command, $output);
    }

    /**
     * Format common arguments.
     *
     * @return string
     */
    protected function _getBaseArguments()
    {
        $user = $this->_database['username'];
        $host = $this->_database['host'];
        $port = '';
        if (!empty($this->_database['port'])) {
            $port = " --port=" . $this->_database['port'];
        }

        return "--host=$host $port --username=$user";
    }

    /**
     * Set current db password.
     *
     * @return void
     */
    protected function _setPassword()
    {
        $password = $this->_database['password'];
        putenv("PGPASSWORD=$password");
    }
}
