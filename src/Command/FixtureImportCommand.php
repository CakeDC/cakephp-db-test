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
namespace CakeDC\DbTest\Command;

use Cake\Command\Command;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use CakeDC\DbTest\Engine\EngineFactory;
use function Cake\I18n\__d;

/**
 * FixtureImportCommand
 *
 * DbTest fixture importer, dumps the template database
 */
class FixtureImportCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(
                __d(
                    'cake_d_c/db_test',
                    'DbTest fixture importer, dumps the template database',
                ),
            )
            ->addOption('dump-folder', [
                'short' => 'd',
                'help' => __d('cake_d_c/db_test', 'Provides path to dump test_db.sql file.'),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(): int
    {
        try {
            $skeletonDatabase = ConnectionManager::get('test_template')->config();
        } catch (MissingDatasourceConfigException) {
            $this->io->error(
                __d('cake_d_c/db_test', 'Missing `test_template` datasource configuration'),
            );

            return self::CODE_ERROR;
        }
        $engine = EngineFactory::engine($skeletonDatabase);
        $skeletonName = $skeletonDatabase['database'];

        $dumpFolder = $this->args->getOption('dump-folder');
        if (!is_string($dumpFolder) || $dumpFolder === '') {
            $dumpFolder = CONFIG . 'sql';
        }
        if (!is_dir($dumpFolder)) {
            mkdir(
                directory: $dumpFolder,
                permissions: 0755,
                recursive: true,
            );
        }
        $dumpFile = $dumpFolder . DS . 'test_db.sql';

        $this->io->out(__d('cake_d_c/db_test', "Exporting data from skeleton database: $skeletonName \n"));
        $engine->export($dumpFile, ['format' => 'plain']);

        return self::CODE_SUCCESS;
    }
}
