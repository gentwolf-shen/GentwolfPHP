<?php

return [
	'mode' => 'debug', // any others: pro etc.
	'key' => '8c8660c8e933debbc97717c075fbc1c4',
	'rewrite' => false,
	'database' => [
		'default' => [
			'dsn' => 'sqlite:../db/gentwolf.db',
			'username' => '',
			'password' => '',
			'prefix' => '',
			'options' => [],
		],
	],
	'view' => [
		'ext' => '.html',
		'cacheTime' => 5,
		'cachePath' => '../application/runtime/view/',
	],
];