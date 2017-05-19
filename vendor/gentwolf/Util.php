<?php

namespace gentwolf;

class Util {
	public static function getRndStr($len) {
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$chars = [];
		for ($i = 0; $i < $len; $i++) {
			$index = mt_rand(0, 61);
			$chars[$i] = $str[$index];
		}
		return implode('', $chars);
	}

	/**
	 * 从config目录的errorCode中取出信息，并输出
	 * @param int $code
	 */
	public static function writeMsg($code = 0) {
		$msg = Gentwolf::loadConfig('messageCode', (string)$code);
		Context::jsonMsg($code, $msg);
	}
}