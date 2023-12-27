<?php

global $URL_PATH, $REST_REQUEST, $REST_REPLY;

use classes\cJSON;

require_once ('common.php');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
header('Access-Control-Allow-Origin: '. $_SERVER['HTTP_HOST'] );
header('X-XSS-Protection: 1; mode=block');
/** @noinspection SpellCheckingInspection */
header('X-Content-Type-Options=nosniff');
header('X-Frame-Options: DENY');

// Parse request body
$REST_REQUEST = array();
if (isset($_SERVER['CONTENT_TYPE'])) {
  switch ($_SERVER['CONTENT_TYPE']) {
    case 'application/json': // REST request

      $REST_REQUEST = json_decode(file_get_contents("php://input"), true);
      if ($REST_REQUEST === null) {
        $REST_REQUEST = array();
        $REST_REPLY->setResult(null, -1, 'Failed to parse request body');
        $URL_PATH[0] = 'error';
      }

      break;

    default: // No idea
      $REST_REPLY->setResult(null, -1, 'No idea how to handle ' . $_SERVER['CONTENT_TYPE']);
      $URL_PATH[0] = 'error';
      break;
  }
}

$REST_REPLY = new cJSON();

switch ($URL_PATH[0]) {
  case 'user':
    switch ($URL_PATH[1]) {
      case 'save':
        \classes\cUser::getInstance()->userSave( $REST_REPLY, $REST_REQUEST );
        break;
      case 'restore':
        \classes\cUser::getInstance()->userRestore( $REST_REPLY, $REST_REQUEST );
        break;
      case 'error': // Internal server error
        http_response_code( 500 ); // Internal server error
        break;
      default:  // 404 NOT  FOUND
        goto default404;
    }
    break;
  case 'resources':
    switch ($URL_PATH[1]) {
      case 'getprice':
        \classes\cResources::getInstance()->getPrice( $REST_REPLY );
        break;
      case 'saleinfo':
        \classes\cResources::getInstance()->getSalesInfo( $REST_REPLY );
        break;
      case 'update':
        \classes\cResources::getInstance()->setAmount( $REST_REPLY, $REST_REQUEST );
        if ($REST_REPLY->RESULT_CODE === 1 ) \classes\cResources::getInstance()->getSalesInfo( $REST_REPLY );
        break;
      case 'error': // Internal server error
        http_response_code( 500 ); // Internal server error
        break;
      default:  // 404 NOT  FOUND
        goto default404;
    }
    break;
  case 'error': // Internal server error
    http_response_code( 500 ); // Internal server error
    break;
  default:  // 404 NOT  FOUND
    default404:
    http_response_code( 404 ); // Page not found
    $REST_REPLY->setResult(null, -1, 'Page not found');
    break;
}

// Return output
echo json_encode( $REST_REPLY->getResult());