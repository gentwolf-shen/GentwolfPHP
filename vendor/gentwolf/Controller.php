<?php

namespace gentwolf;

class Controller {
	private $view = null;

	function __construct() {
	}

	private function initView() {
		if ($this->view == null) {
			$this->view = new View(Gentwolf::config('view'));
		}
	}

	protected function render($tplName, $data = null) {
		$this->initView();
		return $this->view->render($tplName, $data);
	}

	protected function fetch($tplName, $data = null) {
		$this->initView();
		return $this->view->fetch($tplName, $data);
	}

	protected function assign($name, $data = null) {
		$this->initView();
		return $this->view->assign($name, $data);
	}

	protected function clearView() {
		$this->initView();
		$this->view->clear();
	}

}