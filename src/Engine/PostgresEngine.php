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
namespace CakeDC\DbTest\Engine;

use Cake\Log\Log;
use function Cake\I18n\__d;

class PostgresEngine extends BaseEngine
{
    /**
     * @inheritDoc
     */
    public function recreateTestDatabase(): bool
    {
        $baseArgs = $this->getBaseArguments();
        $this->setPassword();
        $databaseName = $this->database['database'];
        $systemUser = 'postgres';
        $terminateQuery = 'select pg_terminate_backend(pg_stat_activity.pid) from pg_stat_activity ' .
            "where pg_stat_activity.datname = '$databaseName'";
        $this->execute("psql $baseArgs -c \"$terminateQuery\" $systemUser", $output, $success);

        $output = [];
        $success = 0;
        Log::info(__d('cake_d_c/db_test', "Dropping database: $databaseName \n"));
        $this->execute("dropdb $baseArgs $databaseName", $output, $success);

        if ($this->isSuccess($success)) {
            Log::info(__d('cake_d_c/db_test', "Creating database: $databaseName \n"));
            $this->execute("createdb $baseArgs $databaseName", $output, $success);
        }

        return $this->isSuccess($success);
    }

    /**
     * @inheritDoc
     */
    public function createSchema(): bool
    {
        $baseArgs = $this->getBaseArguments();
        $this->setPassword();
        $success = 0;
        $testDbName = $this->database['database'];
        if (!empty($this->database['schema'])) {
            $schema = $this->database['schema'];
        }

        if (!empty($schema)) {
            $this->execute("psql $baseArgs -c \"create schema $schema;\" $testDbName", $output, $success);
        }

        return $this->isSuccess($success);
    }

    /**
     * @inheritDoc
     */
    public function import(string $file, array $options = []): bool
    {
        $baseArgs = $this->getBaseArguments();
        $testDbName = $this->database['database'];
        $this->setPassword();

        if (isset($options['format']) && $options['format'] == 'plain') {
            $command = "psql $baseArgs $testDbName < $file";
        } else {
            $command = "pg_restore $baseArgs -j 8 -Fc -d $testDbName $file";
        }

        return $this->execute($command, $output);
    }

    /**
     * @inheritDoc
     */
    public function export(string $file, array $options = []): bool
    {
        $baseArgs = $this->getBaseArguments();
        $this->setPassword();
        $testDbName = $this->database['database'];
        $format = ' -Fc ';
        if (isset($options['format']) && $options['format'] == 'plain') {
            $format = ' -Fp ';
        }

        $command = "pg_dump $baseArgs  --compress=0 --file=$file $format $testDbName";

        return $this->execute($command, $output);
    }

    /**
     * Format common arguments.
     *
     * @return string
     */
    protected function getBaseArguments(): string
    {
        $args = [];
        if (!empty($this->database['host'])) {
            $args[] = '--host=' . $this->database['host'];
        }
        if (!empty($this->database['port'])) {
            $args[] = '--port=' . $this->database['port'];
        }
        if (!empty($this->database['username'])) {
            $args[] = '--username=' . $this->database['username'];
        }

        return implode(' ', $args);
    }

    /**
     * Set current db password.
     *
     * @return void
     */
    protected function setPassword(): void
    {
        if (!isset($this->database['password'])) {
            return;
        }
        putenv('PGPASSWORD=' . $this->database['password']);
    }
}
