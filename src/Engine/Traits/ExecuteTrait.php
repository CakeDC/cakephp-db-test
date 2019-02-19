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
namespace CakeDC\DbTest\Engine\Traits;

use Cake\Log\Log\Log;
/**
 * For executing commands
 */
trait ExecuteTrait
{
    /**
     * Execute an external program
     *
     * @param string $command       The command that will be executed.
     * @param array  $output        Command output
     * @param int    $return_var    Return status of the executed command
     * @return string The last line from the result of the command
     */
    protected function _execute($command, &$output = null, &$return_var = null)
    {
        if ($this->_verbose) {
            Log::info(__d('cake_d_c/db_test', $command . "\n"));
        }
        $result = exec($command, $output, $return_var);
        if ($this->_verbose) {
            Log::info(__d('cake_d_c/db_test', implode("\n", $output) . "\n"));
        }

        return $result;
    }
}
