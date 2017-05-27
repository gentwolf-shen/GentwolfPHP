<?php

namespace manage\model;

use gentwolf\Gentwolf;
use gentwolf\DatabaseHelper;
use gentwolf\Model;
use gentwolf\Cookie;
use gentwolf\Context;
use gentwolf\Util;

class PassportModel extends Model {
	private static $user = null;
	private static $cookieName = 'gwToken';

	public static function login($username, $password) {
		$row = DatabaseHelper::fetchRow('admin', 'id,username', [
			'username' => $username,
			'password' => md5($password),
		]);
		if (!$row) return false;

		return self::saveStatus($row);
	}

	public static function saveStatus($data) {
		$user = [
			'id' => $data['id'],
			'username' => $data['username'],
			'loginTime' => time(),
		];
		$str = json_encode($user);
		$code = Util::authCode($str, Gentwolf::config('key'), 'encode');

		return Cookie::set(self::$cookieName, $code);
	}

	public static function logout() {
		return Cookie::delete(self::$cookieName);
	}

	/**
	 * 取登录的用户基本信息
	 * @return object|null
	 */
	public static function getUser() {
		if (self::$user == null) {
			$str = Cookie::get(self::$cookieName);
			if ($str) {
				$str = Util::authCode($str, Gentwolf::config('key'), 'decode');
				if ($str) {
					$obj = json_decode($str);
					if (is_object($obj)) {
						self::$user = $obj;
					}
				}
			}
		}
		return self::$user;
	}

	public static function hasRight($action = '', $isRedirect = true) {
		if (!self::getUser()) {
			if ($isRedirect) {
				Context::redirect('?manage/passport');
			} else {
				return false;
			}
		}

		return true;
	}
}