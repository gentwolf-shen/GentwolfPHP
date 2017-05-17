<?php

namespace gentwolf;

class Cookie {
	public static function set($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false) {
		if (headers_sent()) return false;
		if (0 != $expire) $expire += time();
		return setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}


	public static function get($key, $default = '') {
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
	}


	public static function delete() {
		$args = func_get_args();
		foreach ($args as $key) {
			self::set($key, '', -86400);	
		}
	}

	public static function all() {
		return $_COOKIE;
	}

	public static function clear() {
		$items = self::all();
        if ($items) {
            foreach ($items as $item) {
                self::delete($item);
            }
        }
	}
}