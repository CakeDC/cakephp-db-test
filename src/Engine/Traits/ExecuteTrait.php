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
namespace CakeDC\DbTest\Engine\Traits;

use Cake\Log\Log;
use function Cake\I18n\__d;

/**
 * For executing commands
 */
trait ExecuteTrait
{
    /**
     * Execute an external program
     *
     * @param string $command The command that will be executed.
     * @param array|null $output Command output
     * @param int|null $return_var Return status of the executed command
     * @return bool The last line from the result of the command
     */
    protected function _execute(
        string $command,
        ?array &$output = null,
        ?int &$return_var = null
    ): bool {
        if ($this->_verbose) {
            Log::info(__d('cake_d_c/db_test', $command . "\n"));
        }
        $result = exec($command, $output, $return_var);
        if ($this->_verbose) {
            Log::info(__d('cake_d_c/db_test', implode("\n", $output) . "\n"));
        }

        return (bool)$result;
    }
}
