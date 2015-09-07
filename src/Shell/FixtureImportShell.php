<?php

namespace DbTest\Shell; 

use Cake\Core\Configure;
use Cake\Console\Shell;
use Cake\Filesystem\Folder;
use DbTest\TestSuite\Fixture\FixtureInjector;

class FixtureImportShell extends Shell {

/**
 * Import fixture shell main method
 *
 * @return void
 */
	public function main() {
		// $this->import();
	}

/**
 * Get & configure the option parser
 *
 * @return void
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(__('DbTest fixture importer:'))
			->addOption('import-structure', array(
				'boolean' => true,
				'default' => false,
				'help' => __('Creates table structure in template schema. By default imports data only.')
			))
			->addOption('plugin', array(
				'help' => __('Load fixture from the plugin folder.')
			))
			->addOption('fixture', array(
				'help' => __('Comma separated list of directories to exclude.')
			));
	}

/**
 * Creates fresh dump of templates schema
 *
 * @return void
 */
	public function dump() {
		Configure::load('app', 'default', false);
		$skeletonDatabase = Configure::read('Datasources.test_template');
		if (!empty($skeletonDatabase)) {
			$skeletonName = $skeletonDatabase['database'];
			$skeletonUser = $skeletonDatabase['username'];
			$skeletonPassword = $skeletonDatabase['password'];

			$dumpFolder = CONFIG . DS . 'sql';
			$this->_ensureFolder($dumpFolder);
			$dumpFile = $dumpFolder . DS . 'test_db.sql';

			print "Exporting data from skeleton database: $skeletonName \n";
			exec("mysqldump --user=$skeletonUser --password=$skeletonPassword $skeletonName  | grep -v '/*!50013 DEFINER' > $dumpFile", $output);
		}
	}

/**
 * Initializes this class with a DataSource object to use as default for all fixtures
 *
 * @return void
 */
	protected function _initDb() {
		if ($this->_initialized) {
			return;
		}
		$db = ConnectionManager::get('test_template');
		$db->cacheSources = false;
		$this->_db = $db;
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $path path
 * @return void
 */
	protected function _ensureFolder($path) {
		$Folder = new Folder($path, true);
	}

}
