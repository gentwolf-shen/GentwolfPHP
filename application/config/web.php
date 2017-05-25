<?php

return [
	'mode' => 'debug', // any others: pro etc.
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