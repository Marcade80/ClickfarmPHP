<?php

class cQueue
{
  private static cQueue $INSTANCE;

  public static function getInstance() : cQueue {
    if (!isset(static::$INSTANCE)) self::$INSTANCE = new cQueue();
    return static::$INSTANCE;
  }

  function __construct() {

  }

  function processQueue( cJSON &$REST_REPLY, $DATA ): int
  {
    foreach ($DATA as $rQueueItem) {
      if (array_key_exists('user', $rQueueItem)) {
        $iResult = $this->processUser( $REST_REPLY, $rQueueItem['user'] );
        if ($iResult !== 1) return $iResult;
      }
      if (array_key_exists('resources', $rQueueItem)) {
        $iResult = $this->processResources( $REST_REPLY, $rQueueItem['resources'] );
        if ($iResult !== 1) return $iResult;
      }
    }

    // Always return resource prices/amounts
    cResources::getInstance()->getPrice( $REST_REPLY );
    cResources::getInstance()->getSalesInfo( $REST_REPLY );

    return $REST_REPLY->RESULT_CODE;
  }

  function processUser( cJSON &$REST_REPLY, $DATA ): int
  {
    if (array_key_exists('save', $DATA)) {
      $iResult = cUser::getInstance()->userSave( $REST_REPLY, $DATA['save'] );
      if ($iResult !== 1) return $iResult;
    }

    return $REST_REPLY->RESULT_CODE;
  }

  function processResources( cJSON &$REST_REPLY, $DATA ): int
  {
    if (array_key_exists('update', $DATA)) {
      $iResult = cResources::getInstance()->setAmount( $REST_REPLY, $DATA['update'] );
      if ($iResult !== 1) return $iResult;
    }
    return $REST_REPLY->RESULT_CODE;
  }
}