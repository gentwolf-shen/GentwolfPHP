<?php

namespace gentwolf;

use Exception;

final class Gentwolf {
	public static $version = '1.0';
	public static $name = 'GentwolfPHP';

	public static $ext = '.php';

	public static $libPath;
	public static $appPath;
	public static $modulePath;

	protected static $libPaths = [];

	private static $config = null;
	private static $userConfig = [];

	public static $module;
	public static $controller;
	public static $action;

	public static $segments;
	public static $urlPrefix;

	public static function run($libPath, $appPath) {
		self::$libPath = $libPath;
		self::$appPath = $appPath;
		self::$modulePath = $appPath .'module/';

		// step 1: lib init
		self::registerAutoload();

		// step 2: request uri
		self::dispatch();
	}

	public static function loadClass($className) {
		$className  = str_replace('\\', '/', $className) . self::$ext;

		$filename = '';
		foreach (self::$libPaths as $path) {
			$tmp = $path . $className;
			//echo "check file ", $tmp;
			if (is_file($tmp)) {
				$filename = $tmp;
				//echo "\t-->\t OK <br />";
				break;
			}
			//echo "<br />";
		}

		if (empty($filename)) {
			throw new Exception('file '. $className .' not found!');
			//throw new ClassNotFoundException('file '. $className .' not found!');
		}

		require_once $filename;
	}

	private static function registerAutoload() {
		self::$libPaths[] = self::$libPath;
		self::$libPaths[] = self::$modulePath;

		spl_autoload_register(function($className){
			Gentwolf::loadClass($className);
		});
	}

	private static function parseUri() {
		$uri = trim(Context::getStr('r'), '/');
		if (empty($uri)) {
			$uri = str_replace(Context::server('SCRIPT_NAME'), '', Context::server('REQUEST_URI'));
			$uri = trim($uri, '/?');
		}

		if (empty($uri)) {
			self::$module = 'site';
			self::$controller = 'Default';
			self::$action = 'default';
		} else {
			$tmp = preg_split('/[!@#$%^&*()_\|+_`~=?\><,.]/i', $uri);
			$uri = $tmp[0];

			$segments = explode('/', $uri);
			if (!is_dir(self::$modulePath . $segments[0])) {
				array_unshift($segments, 'site');
			}

			switch (count($segments)) {
				case 1:
					$segments[1] = 'Default';
					$segments[2] = 'default';
					break;
				case 2:
					$segments[2] = 'default';
					break;
			}

			self::$module = $segments[0];
			self::$controller = ucfirst($segments[1]);
			self::$action = $segments[2];

			self::$segments = array_slice($segments, 3);
		}
	}

	private static function dispatch() {
		self::parseUri();

		$controller = self::$controller . 'Controller';
		$className = '\\'. self::$module .'\controller\\'. $controller;
		$obj = new $className();

		if (method_exists($obj, 'init')) {
			$bl = $obj->init();
			if ($bl === false) {
				return;
			}
		}

		$action = self::$action . 'Action';
		if (method_exists($obj, $action)) {
			call_user_func([$obj, $action], self::$segments);
		} else {
			throw new Exception('Action "'. $action .'" not found in '. $controller);
		}
	}

	// 默认的配置文件
	public static function config($key = null) {
		if (self::$config == null) {
			self::$config = require_once self::$modulePath . self::$module .'/config/web.php';
		}

		return $key == null ? self::$config : (isset(self::$config[$key]) ? self::$config[$key] : null);
	}

	// 用户的配置文件
	public static function loadConfig($name, $key = null) {
		$config = isset(self::$userConfig[$name]) ? self::$userConfig[$name] : null;

		if ($config === null) {
			$filename = self::$modulePath . self::$module .'/config/'. $name .'.php';
			if (!is_file($filename)) {
				$filename = self::$appPath .'/config/'. $name .'.php';
			}

			if (!is_file($filename)) {
				throw new Exception('config "'. $filename .'" not found');
			}

			$config = require_once $filename;
			self::$userConfig[$name] = $config;
		}
		return $key === null ? $config : (isset($config[$key]) ? $config[$key] : null);
	}

	public static function url($str) {
		if (self::config('rewrite')) {
			$str = str_replace('?', '&', $str);
		}

		return self::$urlPrefix . $str;
	}
}