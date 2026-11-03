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

use Cake\Core\Configure;
use Cake\Log\Log;
use function Cake\I18n\__d;

class MysqlEngine extends BaseEngine
{
    /**
     * @inheritDoc
     */
    public function recreateTestDatabase(): bool
    {
        $databaseName = $this->database['database'];
        $baseArgs = $this->getBaseArguments();
        $output = [];
        $success = 0;
        Log::info(__d('cake_d_c/db_test', "Dropping database: $databaseName \n"));
        $this->execute("mysqladmin -f $baseArgs drop $databaseName", $output, $success);
        if ($this->isSuccess($success)) {
            Log::info(__d('cake_d_c/db_test', "Creating database: $databaseName \n"));
            $this->execute("mysqladmin -f $baseArgs create $databaseName", $output, $success);
        }

        return $success === 0;
    }

    /**
     * @inheritDoc
     */
    public function import(string $file, array $options = []): bool
    {
        $databaseName = $this->database['database'];
        $baseArgs = $this->getBaseArguments();
        $command = "mysql $baseArgs $databaseName < $file";

        return $this->execute($command, $output);
    }

    /**
     * @inheritDoc
     */
    public function export(string $file, array $options = []): bool
    {
        $databaseName = $this->database['database'];
        $baseArgs = $this->getBaseArguments();
        $command = 'mysqldump';
        if (Configure::read('DbTest.dumpExtendedInserts') !== true) {
            $command .= ' --extended-insert=FALSE';
        }
        if (Configure::read('DbTest.dumpNoTablespaces') === true) {
            $command .= ' --no-tablespaces';
        }
        $command .= " $baseArgs $databaseName | grep -v -a '/*!50013 DEFINER'";
        if (!empty($file)) {
            $command .= " > $file";
        }

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
            $args[] = '--user=' . $this->database['username'];
        }
        if (isset($this->database['password'])) {
            $args[] = '--password=' . escapeshellarg((string)$this->database['password']);
        }

        return implode(' ', $args);
    }
}
