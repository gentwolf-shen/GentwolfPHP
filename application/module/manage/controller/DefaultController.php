<?php

namespace manage\controller;

use gentwolf\Gentwolf;
use gentwolf\Controller;
use manage\model\PassportModel;

class DefaultController extends Controller {
	public function defaultAction() {
		PassportModel::hasRight();

		$this->render('default/default', [
			'user' => 'admin',
            'items' => Gentwolf::loadConfig('leftNav'),
        ]);
	}
}