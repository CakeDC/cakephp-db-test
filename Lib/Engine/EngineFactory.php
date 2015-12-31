<?php

App::uses('EngineInterface', 'DbTest.Lib/MysqlEngine');

class EngineFactory {

/**
 * Creates new engine instance.
 *
 * @param array $database Database configuration.
 * @return BaseEngine
 */
	public static function engine($database) {
		if (empty($database['datasource'])) {
			throw new NotFoundException(__('Datasource is not defined'));
		}
		$type = str_replace('Database/', '', $database['datasource']);
		$supported = array('Mysql', 'Postgres');
		if (!in_array($type, $supported)) {
			throw new NotFoundException(__('Database engine is not supported'));
		}
		$engineType = $type . 'Engine';
		App::uses($engineType, 'DbTest.Lib/Engine');
		if (!class_exists($engineType)) {
			throw new NotFoundException(__('Can\'t load engine ' . $engineType));
		}
		return new $engineType();
	}

}

