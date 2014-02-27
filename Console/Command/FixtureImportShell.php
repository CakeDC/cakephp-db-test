<?php

App::uses('AppShell', 'Console/Command');
App::uses('CakeTestFixture', 'TestSuite/Fixture');

class FixtureImportShell extends AppShell {

/**
 * Import fixture shell main method
 *
 * @return void
 */
	public function main() {
		$this->import();
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
 * Import fixture action
 *
 * @return void
 */
	public function import() {
		$this->_importDbConfig();
		$name = null;
		if (!empty($this->params['fixture'])) {
			$name = $this->params['fixture'];
		} elseif (!empty($this->args[0])) {
			$name = $this->params['fixture'] = $this->args[0];
		}

		if (empty($name)) {
			$this->out(__('Please define fixture name as first param of shell.'));
			$this->_stop();
		}
		
		$this->_initDb();
	
		$plugin = '';
		if (!empty($this->params['plugin'])) {
			$plugin = $this->params['plugin'] . '.';
		}
		
		$class = Inflector::camelize($name) . 'Fixture';
		$table = Inflector::pluralize(Inflector::underscore($name));
		App::uses($class, $plugin . 'Test/Fixture');
		$this->Fixture = new $class();
		$this->Fixture->Schema = new CakeSchema(array('name' => 'TestSuite', 'connection' => 'test_template'));
		
		$schema = $this->Fixture->Schema->read();
		$tables = $schema['tables'];
		$fields = array();
		if (isset($tables[$table])) {
			$fields = $tables[$table];
		} elseif (isset($tables['missing']) && isset($tables['missing'][$table])) {
			$fields = $tables['missing'][$table];
		}
		unset($fields['indexes'], $fields['tableParameters']);
		$fields = array_keys($fields);

		if (!empty($this->params['import-structure'])) {
			$this->create($this->_db, $this->Fixture);
		}
		$this->insert($this->_db, $this->Fixture, $fields);
		$this->out(__('Data imported successfully'));
	}

/**
 * Creates fresh dump of templates schema
 *
 * @return void
 */
	public function dump() {
		$this->_importDbConfig();
		$skeletonDatabase = $this->Config->test_template;
		if (!empty($skeletonDatabase)) {
			$skeletonName = $skeletonDatabase['database'];
			$skeletonUser = $skeletonDatabase['login'];
			$skeletonPassword = $skeletonDatabase['password'];

			$dumpFolder = ROOT . DS . APP_DIR . DS . 'Config' . DS . 'sql';
			$this->_ensureFolder($cacheFolder);
			$dumpFile = $dumpFolder . DS . 'test_db.sql';

			print "Exporting data from skeleton database: $skeletonName \n";
			exec("mysqldump --user=$skeletonUser --password=$skeletonPassword $skeletonName > $dumpFile", $output);
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
		$db = ConnectionManager::getDataSource('test_template');
		$db->cacheSources = false;
		$this->_db = $db;
	}

/**
 * Run before all tests execute, should return SQL statement to create table for this fixture could be executed successfully.
 *
 * @param DboSource $db An instance of the database object used to create the fixture table
 * @return boolean True on success, false on failure
 */
	public function create($db, $fixture) {
		if (!isset($fixture->fields) || empty($fixture->fields)) {
			return false;
		}
		$fixture->Schema->build(array($fixture->table => $fixture->fields));
		try {
			$db->execute($db->createSchema($fixture->Schema), array('log' => false));
			$fixture->created[] = $db->configKeyName;
		} catch (Exception $e) {
			$msg = __(
				'Fixture creation for "%s" failed "%s"',
				$fixture->table,
				$e->getMessage()
			);
			CakeLog::error($msg);
			trigger_error($msg, E_USER_WARNING);
			return false;
		}
		return true;
	}

/**
 * Run before each tests is executed, should return a set of SQL statements to insert records for the table
 * of this fixture could be executed successfully.
 *
 * @param DboSource $db An instance of the database into which the records will be inserted
 * @return boolean on success or if there are no records to insert, or false on failure
 */
	public function insert($db, $fixture, $actualFields = array()) {
		if (!isset($this->_insert)) {
			$values = array();
			if (isset($fixture->records) && !empty($fixture->records)) {
				$fields = array();
				foreach ($fixture->records as $record) {
					$fields = array_merge($fields, array_keys(array_intersect_key($record, $fixture->fields)));
				}				
				$fields = array_unique($fields);
				if ( !empty($actualFields)) {
					$fields = array_intersect($actualFields, $fields);
				}
				$default = array_fill_keys($fields, null);
				foreach ($fixture->records as $record) {
					$addRecord = array();
					foreach ($record as $k => $v) {
						if (empty($actualFields) || !empty($actualFields) && in_array($k, $actualFields)) {
							$addRecord[$k] = $v;
						}
					}
					$values[] = array_values(array_merge($default, $addRecord));
				}
				$nested = $db->useNestedTransactions;
				$db->useNestedTransactions = false;
				$result = $db->insertMulti($fixture->table, $fields, $values);
				if (
					$fixture->primaryKey &&
					isset($fixture->fields[$fixture->primaryKey]['type']) &&
					in_array($fixture->fields[$fixture->primaryKey]['type'], array('integer', 'biginteger'))
				) {
					$db->resetSequence($fixture->table, $fixture->primaryKey);
				}
				$db->useNestedTransactions = $nested;
				return $result;
			}
			return true;
		}
	}

/**
 * Imports database configuration settings
 *
 * @return void
 */
	protected function _importDbConfig() {
		include_once APP . 'Config' . DS . 'database.php';
		if (class_exists('DATABASE_CONFIG')) {
			$this->Config = new DATABASE_CONFIG();
		}	
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $path
 * @return void
 */
	protected function _ensureFolder($path) {
		$Folder = new Folder($path, true);
	}

}
