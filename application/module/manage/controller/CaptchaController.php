<?php
/**
 * Created by PhpStorm.
 * User: jerry
 * Date: 17-5-27
 * Time: 下午2:51
 */

namespace manage\controller;

use gentwolf\Captcha;

class CaptchaController {
	public function defaultAction() {
		Captcha::image(4, 130);
	}
}