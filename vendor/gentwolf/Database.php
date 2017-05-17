<?php

namespace gentwolf;

use Exception;
use gentwolf\driver\PdoDriver;

class Database {
	private static $instances = [];

	public static function driver($name = 'default') {
		$instance = isset(self::$instances[$name]) ? self::$instances[$name] : null;
		if ($instance == null) {
			$dbConfig = Gentwolf::config('database');			
			if (!isset($dbConfig[$name])) {
				throw new Exception('config "'. $name .'" not found!');
			}
			$instance = new PdoDriver($dbConfig[$name], Gentwolf::config('mode'));
			self::$instances[$name] = $instance;
		}
		return $instance;
	}
}