<?php

App::uses('BaseEngine', 'DbTest.Lib/Engine');

class PostgresEngine extends BaseEngine {

/**
 * Recreates test database.
 *
 * @param array $database Database configuration.
 * @return bool
 */
	public function recreateTestDatabase($database) {
		$baseArgs = $this->_getBaseArguments($database);
		$this->_setPassword($database);
		$databaseName = $database['database'];
		$systemUser = 'postgres';

		$versionQuery = "SHOW SERVER_VERSION";
		$this->_execute("psql $baseArgs -c \"$versionQuery\" $systemUser", $output, $success);
		$version = trim(Hash::get($output, 2));

		// The field was named 'procpid' before 9.2
		$pidFieldName = 'pid';
		if (strnatcmp($version, '9.2') === -1) {
			$pidFieldName = 'procpid';
		}
		$terminateQuery = "select pg_terminate_backend(pg_stat_activity.$pidFieldName) from pg_stat_activity where pg_stat_activity.datname = '$databaseName'";
		$this->_execute("psql $baseArgs -c \"$terminateQuery\" $systemUser", $output, $success);

		$output = array();
		$success = 0;
		print "Dropping database: $databaseName \n";
		$this->_execute("dropdb $baseArgs $databaseName", $output, $success);

		if ($this->isSucess($success)) {
			print "Creating database: $databaseName \n";
			$this->_execute("createdb $baseArgs $databaseName", $output, $success);
		}

		return $this->isSucess($success);
	}

/**
 * Create schema
 *
 * @param array $database Database configuration.
 * @return bool
 */
	public function createSchema($database) {
		$baseArgs = $this->_getBaseArguments($database);
		$this->_setPassword($database);
		$testDbName = $database['database'];
		$schema = $database['schema'];

		$success = 0;
		if (!empty($schema)) {
			$this->_execute("psql $baseArgs -c \"create schema $schema;\" $testDbName", $output, $success);
		}
		return $this->isSucess($success);
	}

/**
 * Import test skeleton database.
 *
 * @param array $database Database configuration.
 * @param string $file Sql file path.
 * @param array $options Additional options/
 * @return bool
 */
	public function import($database, $file, $options = array()) {
		$baseArgs = $this->_getBaseArguments($database);
		$testDbName = $database['database'];
		$this->_setPassword($database);

		if (isset($options['format']) && $options['format'] == 'plain') {
			$command = "psql $baseArgs $testDbName < $file";
		} else {
			$command = "pg_restore $baseArgs -j=8 -Fc -d $testDbName $file";
		}
		return $this->_execute($command, $output);
	}

/**
 * Export database.
 *
 * @param array $database Database configuration.
 * @param string $file Sql file path.
 * @param array $options Additional options/
 * @return bool
 */
	public function export($database, $file, $options = array()) {
		$baseArgs = $this->_getBaseArguments($database);
		$this->_setPassword($database);
		$testDbName = $database['database'];

		$format = ' -Fc ';
		if (isset($options['format']) && $options['format'] == 'plain') {
			$format = " -Fp ";
		}

		$command = "pg_dump $baseArgs  -Z=0 --file=$file $format $testDbName";
		return $this->_execute($command, $output);
	}

/**
 * Format common arguments.
 *
 * @param array $database Database configuration.
 * @return string
 */
	protected function _getBaseArguments($database) {
		$user = $database['login'];
		$host = $database['host'];
		$port = '';
		if (!empty($database['port'])) {
			$port = " --port=" . $database['port'];
		}

		return "--host=$host $port --username=$user";
	}

/**
 * Set current db password.
 *
 * @param array $database Database configuration.
 * @return string
 */
	protected function _setPassword($database) {
		$password = $database['password'];
		putenv("PGPASSWORD=$password");
	}

}
