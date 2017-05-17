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


}