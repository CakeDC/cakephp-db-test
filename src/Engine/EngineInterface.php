<?php

namespace DbTest\Engine;

interface EngineInterface
{

    /**
     * Recreates test database.
     *
     * @param array $database Database configuration.
     * @return bool
     */
    public function recreateTestDatabase($database);

    /**
     * Import test skeleton database.
     *
     * @param array  $database Database configuration.
     * @param string $file     Sql file path.
     * @param array  $options  Additional options/
     * @return bool
     */
    public function import($database, $file, $options = []);

    /**
     * Export database.
     *
     * @param array  $database Database configuration.
     * @param string $file     Sql file path.
     * @param array  $options  Additional options/
     * @return bool
     */
    public function export($database, $file, $options = []);
}
