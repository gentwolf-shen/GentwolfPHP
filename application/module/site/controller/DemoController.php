<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 17-5-17
 * Time: 下午2:08
 */

namespace site\controller;

use gentwolf\Captcha;
use gentwolf\Controller;

class DemoController extends Controller {
	public function init() {

	}

	public function defaultAction() {
		echo 'default';
	}

	public function viewAction() {
		$this->render('demo/view', [
			'name' => 'Tom',
			'address' => 'shanghai',
		]);
	}

	public function clearViewAction() {
		$this->clearView();
	}

	public function captchaAction() {
		Captcha::generate('12aB');
	}
}