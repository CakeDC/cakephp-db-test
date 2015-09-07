<?php 

namespace DbTest\TestSuite\Fixture;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;

class FixtureManager {

/**
 * Drops existing connections to test database, recreates db,
 * and transfers data from test_skel to test
 *
 * @param array $database
 * @param bool  $importTestSkeleton
 * @param string $sqlFilePath
 * @return bool
 */
	public function setupDatabase($database, $importTestSkeleton = false, $sqlFilePath = null) {
		$password = $database['password'];
		$testDbName = $database['database'];
		$testUser = $database['username'];
		$dbHost = $database['host'];

		$portNumber = '';
		if (!empty($database['port'])) {
			$portNumber = "-p " . $database['port'];
		}
		$success = false;
		if ($this->__recreateTestDatabase($testDbName, $portNumber, $testUser, $password, $dbHost) === 0) {
			$success = true;
		}

		if($success && $importTestSkeleton) {
			$this->__importTestSkeleton($database, $sqlFilePath);
		}

		return $success;
	}

/**
 * Recreate test database
 *
 * @param string $testDbName database name
 * @param string $portNumber database port number
 * @param string $testUser database user
 * @param string $password database password
 * @param string $dbHost database host
 *
 * @return int - shell return code
 */
	private function __recreateTestDatabase($testDbName, $portNumber, $testUser, $password, $dbHost) {
		$output = [];
		$success = 0;
		print "Dropping database: $testDbName \n";
		
		exec("mysqladmin -f --user=$testUser --password=$password --host=$dbHost drop $testDbName", $output, $success);

		if (in_array($success, [0, 1])) {
			print "Creating database: $testDbName \n";
			exec("mysqladmin -f --user=$testUser --password=$password --host=$dbHost create $testDbName", $output, $success);
		}

		return $success;
	}

/**
 * Transfers data from test_skel database to test database.
 * Caches initial back up to increase the speed of future transfers.
 * This cached file is stored in the core's fixture cache
 *
 * mysqlamdin must be in your path and be the proper version for your database
 *
 * @param string $database
 */
	public function transferData($database) {
		$testDbName = $database['database'];
		$testUser = $database['username'];
		$password = $database['password'];
		$dbHost = $database['host'];

		$portNumber = '';
		if (!empty($database['port'])) {
			$portNumber = "-p " . $database['port'];
		}

		$output = [];
		$skeletonDatabase = Configure::read('Datasources.test_template');
		if (!empty($skeletonDatabase)) {
			$skeletonName = $skeletonDatabase['database'];
			$skeletonUser = $skeletonDatabase['username'];
			$skeletonPassword = $skeletonDatabase['password'];
			$skeletonHost = $skeletonDatabase['host'];

			$cacheFolder = CACHE . 'fixtures';
			$this->_ensureFolder($cacheFolder);
			$tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';

			if (!file_exists($tmpFile)) {
				print "Backing up data from skeleton database: $skeletonName \n";
				exec("mysqldump --host=$skeletonHost --user=$skeletonUser --password=$skeletonPassword $skeletonName | grep -v '/*!50013 DEFINER' > $tmpFile", $output);
			}

			print "Restoring data to: $testDbName \n";
			exec("mysql --host=$dbHost --user=$testUser --password=$password $testDbName < $tmpFile", $output);
		}
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $database
 * @param string $sqlFilePath
 */
	private function __importTestSkeleton($database, $sqlFilePath = null) {
		$testDbName = $database['database'];
		$testUser = $database['username'];
		$password = $database['password'];
		$dbHost = $database['host'];

		$portNumber = '';
		if (!empty($database['port'])) {
			$portNumber = "-p " . $database['port'];
		}

		$cacheFolder = CACHE . 'fixtures';
		$this->_ensureFolder($cacheFolder);
		$tmpFile = $cacheFolder . DS . 'db_dump_backup.custom';
		print "Deleting cached file: $tmpFile \n";
		if (is_file($tmpFile)) {
			unlink($tmpFile);
		}

		if (empty($sqlFilePath)) {
			$testSkeletonFile = CONFIG . 'sql' . DS . 'test_db.sql';
		} else {
			$testSkeletonFile = $sqlFilePath;
		}
		print "Importing test skeleton from: $testSkeletonFile \n";
		exec("mysql --host=$dbHost --user=$testUser --password=$password $testDbName < $testSkeletonFile", $output);

		print "Backing up data from skeleton database: $testDbName \n\n";
		exec("mysqldump --host=$dbHost --user=$testUser --password=$password $testDbName | grep -v '/*!50013 DEFINER' > $tmpFile", $output);
	}

/**
 * Find and import test_skel.sql file from app/Config/sql
 *
 * @param string $path
 */
	protected function _ensureFolder($path) {
		$Folder = new Folder($path, true);
	}

}