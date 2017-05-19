<?php

namespace manage\controller;

use gentwolf\Context;
use gentwolf\Controller;
use gentwolf\Util;
use manage\model\PassportModel;

class PassportController extends Controller {
	public function defaultAction() {
		$this->render('passport/default');
	}

	public function loginAction() {
		if (Context::isPost()) {
			$username = Context::postStr('username');
			$password = Context::postStr('password');

			$bl = PassportModel::login($username, $password);
			if ($bl) {
				Context::jsonSuccess(true);
			} else {
				Context::jsonError('登录失败，用户名或密码错误！');
			}			
		}
	}

	public function logoutAction() {
		return PassportModel::logout();
	}
}