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
     * @inheritdoc
     */
    public function recreateTestDatabase(): bool
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
     * @inheritdoc
     */
    public function import(string $file, array $options = []): bool
    {
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
        $command = "mysql $baseArgs $databaseName < $file";

        return $this->_execute($command, $output);
    }

    /**
     * @inheritdoc
     */
    public function export(string $file, array $options = []): bool
    {
        $databaseName = $this->_database['database'];
        $baseArgs = $this->_getBaseArguments();
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

        return $this->_execute($command, $output);
    }

    /**
     * Format common arguments.
     *
     * @return string
     */
    protected function _getBaseArguments(): string
    {
        $user = $this->_database['username'];
        $password = $this->_database['password'];
        $host = $this->_database['host'];
        $port = '';
        if (!empty($this->_database['port'])) {
            $port = ' --port=' . $this->_database['port'];
        }
        $quote = DS === '/' ? "'" : '"';

        return "--host=$host $port --user=$user --password=$quote$password$quote";
    }
}
