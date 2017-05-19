<?php

return [
	'mode' => 'debug', // any others: pro etc.
	'rewrite' => false,
	'database' => [
		'default' => [
			'dsn' => 'mysql:host=127.0.0.1;dbname=sightp_console;charset=utf8',
			'username' => 'root',
			'password' => 'jerry',
			'prefix' => 'db_',
			'options' => [],
		],
		'user' => [
			'dsn' => 'sqlite:app.db',
			'username' => '',
			'password' => '',
			'prefix' => '',
			'options' => [],
		],
	],
	'view' => [
		'ext' => '.php',
		'cacheTime' => 10,
		'cachePath' => '../application/runtime/view/default/',
	],	
];