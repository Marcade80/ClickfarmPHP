<?php

require_once 'common.php';

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Access-Control-Allow-Origin: '. $_SERVER['HTTP_HOST'] );
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options=nosniff');
header('X-Frame-Options: DENY');

require_once(ROOT . '/views/main.php');