<?php

class cUser
{
  private static cUser $INSTANCE;
  private static cDatabase $DB;

  public static function getInstance() : cUser {
    if (!isset(static::$INSTANCE)) self::$INSTANCE = new cUser();
    return static::$INSTANCE;
  }

  function __construct() {
    self::$DB = cDatabase::getINSTANCE(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  }

  function userSave( cJSON &$REST_REPLY, $DATA ): int
  {
    if (!array_key_exists('UID', $DATA)) {
      $REST_REPLY->setResult(null,-1,'ERR_MISSING_UID');
      return $REST_REPLY->RESULT_CODE;
    }
    if (!array_key_exists('Data', $DATA)) {
      $REST_REPLY->setResult(null,-1,'ERR_MISSING_DATA');
      return $REST_REPLY->RESULT_CODE;
    }
    $UID = cDatabase::escape( $DATA['UID']);
    $sData = cDatabase::escape( $DATA['Data']);

    $iNumAffectedRows = 0;
    $aRecord = array(
      'UID' => $UID,
      'Data' => $sData,
      'Update_Timestamp' => time(),
    );
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::UPSERT, 'saves',$aRecord, 'UID="' . $UID . '"' );
    return $REST_REPLY->RESULT_CODE;
  }

  function userRestore( cJSON &$REST_REPLY, $DATA ): int
  {
    if (!array_key_exists('UID', $DATA)) {
      $REST_REPLY->setResult(null,-1,'ERR_MISSING_UID');
      return $REST_REPLY->RESULT_CODE;
    }

    $UID = cDatabase::escape( $DATA['UID']);

    $iNumAffectedRows = 0;
    $aRecord = array();
    self::$DB::doQuery($REST_REPLY, $iNumAffectedRows, DBActions::SELECTONE, 'saves',$aRecord, 'UID="' . $UID . '"' );
    if ($iNumAffectedRows === 0) {
      $REST_REPLY->setResult(null,-2,'ERR_INVALID_UID');
      return $REST_REPLY->RESULT_CODE;
    }
    $REST_REPLY->setData( array('Data' => $aRecord['Data']) );
    return $REST_REPLY->RESULT_CODE;
  }

}