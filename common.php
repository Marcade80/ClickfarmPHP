<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

const TITLE = 'Clickfarm';
const DOMAIN = 'Clickfarm';
const HOME_PATH = '/';

// Home URL
if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
  (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
  (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
  $server_request_scheme = 'https';
} else {
  $server_request_scheme = 'http';
}

define("HOME_URL", $server_request_scheme . '://' . $_SERVER['HTTP_HOST'] . '');
define("ROOT", $_SERVER['DOCUMENT_ROOT']);

// Auto load classes when they are used
spl_autoload_register( function ( string $sClassPath ) : void {
  $sClassFullPath = ROOT . DIRECTORY_SEPARATOR . $sClassPath . '.php';
  if (!file_exists( $sClassFullPath)) { error_log('Class ' . $sClassPath . ' not found.'); return; }

  include_once ( $sClassFullPath );
});

// DB Credentials
require_once(ROOT . '/credentials.php');

// Requested URL
$URL = preg_replace( '/' . preg_quote(HOME_PATH, '/') . '/i', '', $_SERVER['REQUEST_URI'], 1);
if (strpos($URL, '?') > 0) {
  // GET Parameters specified
  $URL = substr($URL, 0, strpos($URL, '?'));
}
$URL_PATH = explode('/', strtolower($URL));

if (count($URL_PATH) > 0) {
  if (strlen($URL_PATH[count($URL_PATH)-1]) == 0) array_pop($URL_PATH);
}
if (count($URL_PATH) == 0) $URL_PATH[] = '';
