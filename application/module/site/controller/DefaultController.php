<?php

namespace site\controller;

use gentwolf\Controller;
use gentwolf\Context;


class DefaultController extends Controller  {
	public function defaultAction() {
		echo 'Hello World';		
	}

	public function phpinfoAction() {
		phpinfo();
	}
}