<?php

App::uses('BaseEngine', 'DbTest.Lib/Engine');

class MysqlEngine extends BaseEngine
{

    /**
     * Recreates test database.
     *
     * @param array $database Database configuration.
     * @return bool
     */
    public function recreateTestDatabase($database)
    {
        $databaseName = $database['database'];
        $baseArgs = $this->_getBaseArguments($database);

        $output = [];
        $success = 0;
        print "Dropping database: $databaseName \n";
        $this->_execute("mysqladmin -f $baseArgs drop $databaseName", $output, $success);

        if (in_array($success, [0, 1])) {
            print "Creating database: $databaseName \n";
            $this->_execute("mysqladmin -f $baseArgs create $databaseName", $output, $success);
        }

        return $success === 0;
    }

    /**
     * Import test skeleton database.
     *
     * @param array $database Database configuration.
     * @param string $file Sql file path.
     * @param array $options Additional options/
     * @return bool
     */
    public function import($database, $file, $options = [])
    {
        $databaseName = $database['database'];
        $baseArgs = $this->_getBaseArguments($database);
        $command = "mysql $baseArgs $databaseName < $file";

        return $this->_execute($command, $output);
    }

    /**
     * Export database.
     *
     * @param array $database Database configuration.
     * @param string $file Sql file path.
     * @param array $options Additional options/
     * @return bool
     */
    public function export($database, $file, $options = [])
    {
        $databaseName = $database['database'];
        $baseArgs = $this->_getBaseArguments($database);
        $command = "mysqldump $baseArgs $databaseName | grep -v '/*!50013 DEFINER'";
        if (!empty($file)) {
            $command .= " > $file";
        }

        return $this->_execute($command, $output);
    }

    /**
     * Format common arguments.
     *
     * @param array $database Database configuration.
     * @return string
     */
    protected function _getBaseArguments($database)
    {
        $user = $database['login'];
        $password = $database['password'];
        $host = $database['host'];
        $port = '';
        if (!empty($database['port'])) {
            $port = " --port=" . $database['port'];
        }

        return "--host=$host $port --user=$user --password=$password";
    }
}
