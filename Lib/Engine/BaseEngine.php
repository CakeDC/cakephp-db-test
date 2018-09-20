<?php

App::uses('EngineInterface', 'DbTest.Lib/Engine');

abstract class BaseEngine
{

    /**
     * Show commands and results on execution
     *
     * @var bool
     */
    protected $_verbose = false;

    /**
     * Recreates test database.
     *
     * @param array $database Database configuration.
     * @return bool
     */
    abstract public function recreateTestDatabase($database);

    /**
     * Import test skeleton database.
     *
     * @param array $database Database configuration.
     * @param string $file Sql file path.
     * @param array $options Additional options/
     * @return bool
     */
    abstract public function import($database, $file, $options = []);

    /**
     * Export database.
     *
     * @param array $database Database configuration.
     * @param string $file Sql file path.
     * @param array $options Additional options/
     * @return bool
     */
    abstract public function export($database, $file, $options = []);

    public function isSucess($check)
    {
        $allowed = [
            0 => true,
            1 => true,
        ];

        return isset($allowed[$check]) && $allowed[$check];
    }

    /**
     * Create schema
     *
     * @param array $database Database configuration.
     * @return bool
     */
    public function createSchema($database)
    {
        return true;
    }

    /**
     * @param string $command The command that will be executed.
     * @param array $output
     * @param int $return_var
     * @return string The last line from the result of the command
     */
    protected function _execute($command, &$output = null, &$return_var = null)
    {
        if ($this->_verbose) {
            print($command . "\n");
        }
        $result = exec($command, $output, $return_var);
        if ($this->_verbose) {
            print(implode("\n", $output) . "\n");
        }

        return $result;
    }
}
