<?php

class cResources
{
  private static int $UPDATE_INTERVAL = 10;

  private static cResources $INSTANCE;
  private static cDatabase $DB;

  public static function getInstance() : cResources {
    if (!isset(static::$INSTANCE)) self::$INSTANCE = new cResources();
    return static::$INSTANCE;
  }

  function __construct() {
    self::$DB = cDatabase::getINSTANCE(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  }

  function setNewPrices(): void
  {
    $REST_REPLY = new cJSON();
    $iNumAffectedRows = 0;

    // Update the prices if more than the interval has passed
    $aResource = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECTONE, 'resources',$aResource, 'ResType="Oil"' );

    if (time() - self::$UPDATE_INTERVAL >= $aResource['Update_Timestamp'] ) {
      $iNewTimestamp = $aResource['Update_Timestamp'] + self::$UPDATE_INTERVAL;
      if (time() - self::$UPDATE_INTERVAL > $iNewTimestamp ) $iNewTimestamp = time() + self::$UPDATE_INTERVAL;

      $aResource = array( 'Price' => rand(1, 21), 'Update_Timestamp' => $iNewTimestamp );
      self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aResource, 'ResType="Oil"' );
      $aResource = array( 'Price' => rand(1, 500), 'Update_Timestamp' => $iNewTimestamp );
      self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aResource, 'ResType="GrainLow"' );
      $aResource = array( 'Price' => rand(1, 250), 'Update_Timestamp' => $iNewTimestamp );
      self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aResource, 'ResType="GrainHigh"' );
      $aResource = array( 'Price' => rand(100, 900), 'Update_Timestamp' => $iNewTimestamp );
      self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aResource, 'ResType="FlourLow"' );
      $aResource = array( 'Price' => rand(350, 450), 'Update_Timestamp' => $iNewTimestamp );
      self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aResource, 'ResType="FlourHigh"' );
    }
  }

  function getPrice( cJSON &$REST_REPLY ): int
  {
    self::setNewPrices();

    $iNumAffectedRows = 0;
    $aRecords = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECT, 'resources',$aRecords );
    $rResources = $REST_REPLY->getDataByKey('resources');
    $rResourcePrice = array();
    foreach ($rResources as $rResource) $rResourcePrice[ $rResource['ResType'] ] = $rResource['Price'];
    $REST_REPLY->setData( $rResourcePrice );
    return $REST_REPLY->RESULT_CODE;
  }

  function getSalesInfo( cJSON &$REST_REPLY ): int
  {
    self::setNewPrices();

    $iNumAffectedRows = 0;
    $aRecords = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECT, 'resources',$aRecords );
    $rResources = $REST_REPLY->getDataByKey('resources');
    $rResourceAmount = array();
    foreach ($rResources as $rResource) $rResourceAmount[ $rResource['ResType'] ] = $rResource['Amount'];
    $REST_REPLY->setData( $rResourceAmount );
    return $REST_REPLY->RESULT_CODE;
  }

  function setAmount( cJSON &$REST_REPLY, $DATA ): int
  {
    if (!array_key_exists('ResType', $DATA)) {
      $REST_REPLY->setResult(null,-1,'ERR_MISSING_RESTYPE');
      return $REST_REPLY->RESULT_CODE;
    }
    if (!array_key_exists('Amount', $DATA)) {
      $REST_REPLY->setResult(null,-1,'ERR_MISSING_AMOUNT');
      return $REST_REPLY->RESULT_CODE;
    }
    $sResType = cDatabase::escape( $DATA['ResType'] );
    $iAmount = intval( $DATA['Amount'] );

    $iNumAffectedRows = 0;
    $aRecord = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECTONE, 'resources',$aRecord, 'ResType="' . $sResType . '"' );
    if ($iNumAffectedRows === 0) {
      $REST_REPLY->setResult(null,-1,'ERR_INVALID_RESTYPE');
      return $REST_REPLY->RESULT_CODE;
    }
    $aRecord['Amount'] = $aRecord['Amount'] + $iAmount;
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPDATE, 'resources',$aRecord, 'ResType="' . $sResType . '"' );

    return $REST_REPLY->RESULT_CODE;
  }
}