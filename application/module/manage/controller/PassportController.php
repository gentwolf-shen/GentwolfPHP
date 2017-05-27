<?php

namespace manage\controller;

use gentwolf\Captcha;
use gentwolf\Context;
use gentwolf\Controller;
use manage\model\PassportModel;

class PassportController extends Controller {
	public function defaultAction() {
		$this->render('passport/default');
	}

	public function loginAction() {
		if (Context::isPost()) {
			$username = Context::postStr('username');
			$password = Context::postStr('password');
			$code = Context::postStr('code');

			if (!Captcha::verify($code)) {
				//Context::jsonError('验证码错误！');
			} else {
				Captcha::clear();
			}

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