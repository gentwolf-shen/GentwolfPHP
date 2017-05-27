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

	/**
	 * 字符串加密
	 * @param $string
	 * @param $key
	 * @param string $operation
	 * @param int $expiry
	 * @return bool|string
	 */
	public static function authCode($string, $key, $operation = 'decode', $expiry = 0) {
		$operation = strtoupper($operation);

		if ('DECODE' == $operation) {
			$string = str_replace('%20', ' ', $string);
			$string = str_replace(' ', '+', $string);
			$string = str_replace('%2F', '/', $string);
		}

		$ckey_length = 4;

		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if(substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
}