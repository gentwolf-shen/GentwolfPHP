<?php

namespace gentwolf;

class Context {
	public static function getStr($name, $default = '') {
		return isset($_GET[$name]) ? trim($_GET[$name]) : $default;
	}

	public static function getInt($name, $default = 0) {
		return isset($_GET[$name]) ? (int)$_GET[$name] : $default;
	}

	public static function get($name) {
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}

	public static function postStr($name, $default = '') {
		return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
	}

	public static function postInt($name, $default = 0) {
		return isset($_POST[$name]) ? (int)$_POST[$name] : $default;
	}

	public static function post($name) {
		return isset($_POST[$name]) ? $_POST[$name] : null;
	}

	public static function getClientIP() {
		return self::server('REMOTE_ADDR');
	}

	public static function server($name, $default = '') {
		return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
	}

	public static function isPost() {
		return self::server('REQUEST_METHOD') == 'POST';
	}

	public static function isGet() {
		return self::server('REQUEST_METHOD') == 'GET';
	}

	public static function isDelete() {
		return self::server('REQUEST_METHOD') == 'DELETE';
	}

	public static function isPut() {
		return self::server('REQUEST_METHOD') == 'PUT';
	}

	public static function redirect($url) {
		header('location:'. $url);
		exit;
	}

	/**
	 * 输出JSON信息
	 * @param int $code 前3位必须是http code支持的状态码
	 * @param mixed $message
	 */
	public static function jsonMsg($code, $message = null) {
		$data = [
			'code' => $code,
			'message' => $message,
		];
		self::writeJSON($data, substr((string)$code, 0, 3));
	}

	public static function jsonSuccess($msg = 'success') {
		$data = null;
		if (is_scalar($msg)) {
			$data = [
				'code' => 0,
				'message' => $msg,
			];
		} else {
			$data = $msg;
		}

		self::writeJSON($data, 200);
	}

	public static function jsonError($msg) {
		$data = [
			'code' => 5000000,
			'message' => $msg,
		];
		self::writeJSON($data, 500);
	}

	/**
	 * 输出JSON
	 * @param mixed $data
	 * @param int $httpCode
	 */
	public static function writeJSON($msg, $httpCode = 200) {
		header('Content-Type: application/javascript; charset=UTF-8');

		$str = is_array($msg) || is_object($msg) ? json_encode($msg) : $msg;

		$callback = self::getStr('callback');
		if ($callback) {
			echo $callback, '(', $str, ')';
		} else {
			echo $str;
		}
		exit;
	}

}