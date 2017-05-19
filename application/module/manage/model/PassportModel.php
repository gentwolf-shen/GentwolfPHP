<?php

namespace manage\model;

use gentwolf\DatabaseHelper;
use gentwolf\Model;
use gentwolf\Cookie;
use gentwolf\Context;

class PassportModel extends Model {
	public static function login($username, $password) {
		$row = DatabaseHelper::fetchRow('admin', 'id,username', [
			'username' => $username,
			'password' => md5($password),
		]);
		if (!$row) return false;

		self::saveStatus($row);
		return $row;
	}

	public static function saveStatus($data) {
		return Cookie::save('admin', $data['username']);
	}

	public static function logout() {
		return Cookie::delete('admin');
	}

	public static function hasRight($action = '', $isRedirect = true) {
		$admin = Cookie::get('admin');
		$isLogined = !empty($admin);
		
		if (!$isLogined) {
			if ($isRedirect) {
				Context::redirect('?manage/passport');
			} else {
				return false;
			}
		}

		return true;
	}
}