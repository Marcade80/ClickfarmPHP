<?php

namespace classes;

class cResources
{
  private static cResources $INSTANCE;
  private static cDatabase $DB;

  public static function getInstance() : cResources {
    if (!isset(static::$INSTANCE)) self::$INSTANCE = new cResources();
    return static::$INSTANCE;
  }

  function __construct() {
    self::$DB = cDatabase::getINSTANCE(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  }

  function getPrice( cJSON &$REST_REPLY ) {
    $iNumAffectedRows = 0;
    $aRecords = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECT, 'resources',$aRecords );
    $rResources = $REST_REPLY->getDataByKey('resources');
    $rResourcePrice = array();
    foreach ($rResources as $rResource) $rResourcePrice[ $rResource['ResType'] ] = $rResource['Price'];
    $REST_REPLY->setData( $rResourcePrice );
    return $REST_REPLY->RESULT_CODE;
  }

  function getSalesInfo( cJSON &$REST_REPLY ) {
    $iNumAffectedRows = 0;
    $aRecords = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECT, 'resources',$aRecords );
    $rResources = $REST_REPLY->getDataByKey('resources');
    $rResourceAmount = array();
    foreach ($rResources as $rResource) $rResourceAmount[ $rResource['ResType'] ] = $rResource['Amount'];
    $REST_REPLY->setData( $rResourceAmount );
    return $REST_REPLY->RESULT_CODE;
  }
}