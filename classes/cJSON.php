<?php

class cJSON
{
  public int $RESULT_CODE = 1;
  protected string $RESULT_REASON = 'SUCCESS';
  public array $DATA = array();

  public function setResult(array $RESULT = null, int $RESULT_CODE = 1, string $RESULT_REASON = 'SUCCESS', array $DATA = array() ): void {
    if ($RESULT === null) {
      $this->RESULT_CODE = $RESULT_CODE;
      $this->RESULT_REASON = $RESULT_REASON;
      $this->DATA = $DATA;
    } else {
      $this->RESULT_CODE = $RESULT['code'];
      $this->RESULT_REASON = $RESULT['reason'];
      if (array_key_exists('data', $RESULT)) {
        $this->DATA = $RESULT['data'];
      } else {
        $this->DATA = array();
      }
    }
  }

  public function setReason( string $RESULT_REASON = 'SUCCESS' ): void {
    $this->RESULT_REASON = $RESULT_REASON;
  }

  public function getDataByKey(string $KEY ) : array {
    if (array_key_exists($KEY, $this->DATA)) return $this->DATA[$KEY];

    return array();
  }

  public function setData( array $DATA = array() ) : void {
    $this->DATA = $DATA;
  }

  public function appendData( array $DATA = array() ) : void {
    foreach ($DATA as $key => $value) $this->DATA[$key] = $value;
  }

  public function removeDataByKey( string $KEY ) : void {
    if (array_key_exists($KEY, $this->DATA)) unset($this->DATA[$KEY]);
  }

  public function getResult(): array {
    $rResult = array( 'code' => $this->RESULT_CODE, 'reason' => $this->RESULT_REASON );

    if ( count($this->DATA) > 0 ) $rResult['data'] = $this->DATA;

    return $rResult;
  }
}