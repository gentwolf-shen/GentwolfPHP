<?php

namespace gentwolf;

class Context {
	public static function getStr($name, $default = '') {
		return isset($_GET[$name]) ? htmlspecialchars(trim($_GET[$name])) : $default;
	}

	public static function getInt($name, $default = 0) {
		return isset($_GET[$name]) ? (int)$_GET[$name] : $default;
	}

	public static function postStr($name, $default = '') {
		return isset($_POST[$name]) ? htmlspecialchars(trim($_POST[$name])) : $default;
	}

	public static function postInt($name, $default = 0) {
		return isset($_POST[$name]) ? (int)$_POST[$name] : $default;
	}

	public static function getClientIP() {
		return '';
	}

	public static function server($name, $default = '') {
		return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
	}

	public static function writeJSON($data) {
		header('Content-Type: application/javascript; charset=UTF-8');

		$str = json_encode($data);

		$callback = self::getStr('callback');
		if ($callback) {
			echo $callback, '(', $str, ')';
		} else {
			echo $str;
		}
	}

}