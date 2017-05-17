<?php

namespace gentwolf\exception;

use Exception;

class ClassNotFoundException extends Exception {
	public function __construct($message = '') {
		$this->message = $message;

	}

	public function __toString() {
		return 'AAAA';
	}
}