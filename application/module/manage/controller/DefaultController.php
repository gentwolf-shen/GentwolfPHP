<?php

namespace manage\controller;

use gentwolf\Gentwolf;
use gentwolf\Controller;
use manage\model\PassportModel;

class DefaultController extends Controller {
	public function defaultAction() {
		PassportModel::hasRight();

		$this->render('default/default', [
			'user' => PassportModel::getUser(),
            'items' => Gentwolf::loadConfig('leftNav'),
        ]);
	}

	public function welcomeAction() {
		$this->render('default/welcome');
	}
}