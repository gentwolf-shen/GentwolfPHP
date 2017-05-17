<?php

/**
 * @Author gentwolf
 * @Date 2017/05/16
 */

namespace gentwolf;

define('LIB_PATH', __DIR__ .'/../vendor/');
define('APP_PATH', __DIR__ .'/../application/');
define('XHPROF_MODE', ''); // empty or background or link

if (XHPROF_MODE != '') {
	require_once LIB_PATH .'gentwolf/XhprofUtil.php';
	XhprofUtil::startXhprof(XHPROF_MODE);
}

require_once LIB_PATH .'gentwolf/Gentwolf.php';
Gentwolf::run(LIB_PATH, APP_PATH);

if (XHPROF_MODE != '') {
	XhprofUtil::stopXhprof();
}