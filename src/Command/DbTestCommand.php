<?php
declare(strict_types=1);

namespace CakeDC\DbTest\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use CakeDC\DbTest\TestSuite\Fixture\FixtureManager;
use function Cake\I18n\__d;

/**
 * DbTestCommand
 *
 * The Db Test Shell extends the CakePhp TestSuite and no longer needs fixtures defined.
 * Instead the test and test-template databases are synchronized before each test class is executed.
 * Transaction wrapping used to rollback test case changes
 */
class DbTestCommand extends Command
{
    /**
     * @inheritdoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription([
                __d('cake_d_c/db_test', 'The Db Test Shell extends the CakePhp TestSuite and no longer needs fixtures defined.
                    Instead the test and test-template databases are synchronized before each test class is executed.
                    Transaction wrapping used to rollback test case changes.'),
            ])
            ->addOption('import-database-template', [
                'boolean' => true,
                'short' => 'i',
                'help' => __d('cake_d_c/db_test', 'Drops test template database and imports test_db.sql file from app/Config/sql'),
            ])
            ->addOption('import-database-file', [
                'short' => 'f',
                'help' => __d('cake_d_c/db_test', 'Provides path to test_db.sql file'),
            ]);
    }

    /**
     * @inheritdoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        Configure::load('app', 'default', false);

        $io->out(__d('cake_d_c/db_test', 'Db Test Shell'));
        $io->hr();

        if ($args->getOption('import-database-template')) {
            $path = null;
            if (
                !empty($args->getOption('import-database-file')) &&
                file_exists($args->getOption('import-database-file'))
            ) {
                $path = $args->getOption('import-database-file');
            }
            $skeletonDatabase = ConnectionManager::get('test_template')->config();
            $manager = new FixtureManager();
            $manager->setupDatabase($skeletonDatabase, true, true, $path);
        } else {
            $io->out($this->getOptionParser()->help());
        }

        return self::CODE_SUCCESS;
    }
}
