<?php
declare(strict_types=1);

namespace CakeDC\DbTest\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionManager;
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
     * @inheritdoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription(
                __d(
                    'cake_d_c/db_test',
                    'DbTest fixture importer, dumps the template database'
                )
            )
            ->addOption('dump-folder', [
                'short' => 'd',
                'help' => __d('cake_d_c/db_test', 'Provides path to dump test_db.sql file.'),
            ]);
    }

    /**
     * @inheritdoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $skeletonDatabase = ConnectionManager::get('test_template')->config();

        if (empty($skeletonDatabase)) {
            $io->error(
                __d('cake_d_c/db_test', 'Missing `test_template` datasource configuration')
            );

            return self::CODE_ERROR;
        }
        $skeletonName = $skeletonDatabase['database'];

        $dumpFolder = $args->getOption('dump-folder') ?? CONFIG . DS . 'sql';
        if (!is_dir($dumpFolder)) {
            mkdir(
                directory: $dumpFolder,
                permissions: 0755,
                recursive: true
            );
        }
        $dumpFile = $dumpFolder . DS . 'test_db.sql';

        $io->out(__d('cake_d_c/db_test', "Exporting data from skeleton database: $skeletonName \n"));
        $engine = EngineFactory::engine($skeletonDatabase);
        $engine->export($dumpFile, ['format' => 'plain']);
    }
}