<?php

namespace DbTest\Engine;

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
        Log::info("Dropping database: $databaseName \n");
        $this->_execute("mysqladmin -f $baseArgs drop $databaseName", $output, $success);
        if ($this->isSuccess($success)) {
            Log::info( "Creating database: $databaseName \n");
            $this->_execute("mysqladmin -f $baseArgs create $databaseName", $output, $success);
        }

        return $success === 0;
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
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
        $command = "mysql $baseArgs $databaseName < $file";

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
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
        $command = "mysqldump $baseArgs $databaseName | grep -v '/*!50013 DEFINER'";
        if (!empty($file)) {
            $command .= " > $file";
        }

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
        $password = $this->_database['password'];
        $host = $this->_database['host'];
        $port = '';
        if (!empty($this->_database['port'])) {
            $port = " --port=" . $this->_database['port'];
        }
        $quote = DS === '/' ? "'" : '"';

        return "--host=$host $port --user=$user --password=$quote$password$quote";
    }
}
