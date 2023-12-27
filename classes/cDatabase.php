<?php

namespace classes;

use AllowDynamicProperties;
use Exception;

enum DBActions : int {
  case SELECT = 0;
  case INSERT = 1;
  case UPDATE = 2;
  case UPSERT = 3;
  case DELETE = 4;
  case SELECTONE = 5;
}

class cDatabase {
  private static bool $DEBUG = true;
  private static cDatabase $INSTANCE;

  private static object $MYSQLI;

  private static array $TABLE_STRUCTURES;

  function __construct(string $DB_Host, string $DB_User, string $DB_Pass, string $DB_Name) {

    // Open the database
    self::$MYSQLI = mysqli_init();
    if (!self::$MYSQLI) throw new Exception('mysqli_init failed');
    if (!self::$MYSQLI->real_connect($DB_Host, $DB_User, $DB_Pass, $DB_Name, null , null , MYSQLI_CLIENT_FOUND_ROWS)) {
      throw new Exception('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }

    mysqli_set_charset(self::$MYSQLI,"utf8mb4");

    // Set up table structures
    self::$TABLE_STRUCTURES = array(
      'saves' => array(
        'table' => 'saves',
        'primary_key' => 'ID',
        'fields' => array(
          'ID'                     => array('type' => 'int'),
          'created'                => array('type' => 'int'),
          'user'                   => array('type' => 'char'),
          'savegame'               => array('type' => 'char'),
          'persistent'             => array('type' => 'int'),
          'modified'               => array('type' => 'int'),
          'saveid'                 => array('type' => 'char'),
        ),
      ),
      'resources' => array(
        'table' => 'resources',
        'primary_key' => 'ID',
        'fields' => array(
          'ID'                     => array('type' => 'int'),
          'ResType'                => array('type' => 'char'),
          'Amount'                 => array('type' => 'int'),
          'Previous'               => array('type' => 'int'),
          'Price'                  => array('type' => 'int'),
          'PriceIndex'             => array('type' => 'int'),
        ),
      ),
      'sales' => array(
        'table' => 'sales',
        'primary_key' => 'ID',
        'fields' => array(
          'ID'                     => array('type' => 'int'),
          'user'                   => array('type' => 'char'),
          'item'                   => array('type' => 'int'),
          'profit'                 => array('type' => 'int'),
          'created'                => array('type' => 'int'),
          'amount'                 => array('type' => 'int'),
        ),
      ),
    );
  }

  function __destruct() {
    if (isset(self::$MYSQLI)) {
      self::$MYSQLI->close();
    }
  }

  public static function getInstance(string $DB_Host = '', string $DB_User = '', string $DB_Pass = '', string $DB_Name = '') : cDatabase {
    if (!isset(static::$INSTANCE)) {
      if ($DB_Host === '') {
        throw new Exception('Cannot get cDatabase instance without DBHost/User/Pass/Name.');
      } else {
        self::$INSTANCE = new cDatabase($DB_Host, $DB_User, $DB_Pass, $DB_Name);
      }
    }
    return static::$INSTANCE;
  }

  public static function escape(string $sValue) : string {
    return self::$MYSQLI->real_escape_string( $sValue );
  }

  public static function doQuery(cJSON &$REST_REPLY, int &$iNumAffectedRows, DBActions $eAction, string $sTable , array &$aRecord = null, string $sCondition = 'TRUE') : int {
    $sQuery = '';

    if (!array_key_exists($sTable, self::$TABLE_STRUCTURES)) {
      $REST_REPLY->setResult(null,-2,'ERR_DB_TABLE_NOT_CONFIGURED[' . $sTable . ']');
      return $REST_REPLY->RESULT_CODE;
    }

    $aFields = array();
    $sUpsert = '';

    // Validate fields
    if ($aRecord) {

      foreach ( $aRecord as $sField => $sValue ) {
        if (!array_key_exists($sField, self::$TABLE_STRUCTURES[$sTable]['fields'])) {
          $REST_REPLY->setResult(null,-2,'ERR_DB_FIELD_NONEXISTENT_IN_TABLE[' . $sField . ']');
          return $REST_REPLY->RESULT_CODE;
        }
        $aFields[] = $sField;
        if (($eAction == DBActions::SELECT)||($eAction == DBActions::INSERT)||($eAction == DBActions::UPDATE)||($eAction == DBActions::UPSERT) ) {
          if ($sValue === null) {
            $sUpsert .= $sField . '=NULL, ';
          } else {
            switch (self::$TABLE_STRUCTURES[$sTable]['fields'][$sField]['type']) {
              case 'char':
                $sUpsert .= $sField . '="' . self::$MYSQLI->real_escape_string($sValue) . '", ';
                break;
              case 'int':
                if ((!is_numeric($sValue))&&(!is_float($sValue))) {
                  $REST_REPLY->setResult(null,-2,'ERR_DB_FIELD_VALUE_NOT_NUMERIC[' . $sField . ']');
                  return $REST_REPLY->RESULT_CODE;
                }
                $sUpsert .= $sField . '=' . $sValue . ', ';
                break;
              default:
                $REST_REPLY->setResult(null,-2,'ERR_DB_FIELD_TYPE_INVALID[' . $sField . ']');
                return $REST_REPLY->RESULT_CODE;
            }
          }
        }
      }
      if (($eAction == DBActions::SELECT)||($eAction == DBActions::INSERT)||($eAction == DBActions::UPDATE)||($eAction == DBActions::UPSERT) ) $sUpsert = substr($sUpsert, 0, -2);
    } else {
      if ($eAction == DBActions::UPSERT) {
        $REST_REPLY->setResult(null,-2,'ERR_DB_CANNOT_UPSERT_DATA');
        return $REST_REPLY->RESULT_CODE;
      }
      if ( ($eAction == DBActions::INSERT)||($eAction == DBActions::UPDATE)||($eAction == DBActions::DELETE) ) {
        if (!array_key_exists($sTable, $REST_REPLY->DATA)) {
          $REST_REPLY->setResult(null,-2,'ERR_DB_NO_DATA[' . $sTable . ']');
          return $REST_REPLY->RESULT_CODE;
        }

        // Start a transaction
        self::$MYSQLI->begin_transaction();

        foreach($REST_REPLY->DATA[ $sTable ] as $aSubRecord) {
          if (($eAction == DBActions::UPDATE)||($eAction == DBActions::DELETE)) {
            if (!array_key_exists(self::$TABLE_STRUCTURES[$sTable]['primary_key'], $aSubRecord)) {
              self::$MYSQLI->rollback();
              $REST_REPLY->setResult(null,-2,'ERR_DB_DATA_MISSINGKEY[' . self::$TABLE_STRUCTURES[$sTable]['primary_key'] . ']');
              return $REST_REPLY->RESULT_CODE;
            }

            $tmp_REST_REPLY = new cJSON();
            $tmp_iNumAffectedRows = 0;
            $iResult = self::doQuery( $tmp_REST_REPLY, $tmp_iNumAffectedRows, $eAction, $sTable, $aSubRecord, $sCondition );
            if ($iResult < 0) {
              self::$MYSQLI->rollback();
              $REST_REPLY = $tmp_REST_REPLY;
              return $REST_REPLY->RESULT_CODE;
            }
            $iNumAffectedRows = $iNumAffectedRows + $tmp_iNumAffectedRows;
          }
        }

        self::$MYSQLI->commit();
        return $REST_REPLY->RESULT_CODE;
      }
    }

    // Check if the record exists already
    if ($eAction == DBActions::UPSERT) {
      $tmp_REST_REPLY = new cJSON();
      $tmp_iNumAffectedRows = 0;
      $tmp_Record = array();
      $iResult = self::doQuery( $tmp_REST_REPLY, $tmp_iNumAffectedRows, DBActions::SELECTONE, $sTable, $tmp_Record, $sCondition );
      if ($iResult < 0) {
        $REST_REPLY = $tmp_REST_REPLY;
        return $REST_REPLY->RESULT_CODE;
      }
      if ($tmp_iNumAffectedRows > 0 ) {
        $aRecord[ self::$TABLE_STRUCTURES[$sTable]['primary_key'] ] = $tmp_Record[ self::$TABLE_STRUCTURES[$sTable]['primary_key'] ];
        $eAction = DBActions::UPDATE;
      } else {
        $eAction = DBActions::INSERT;
      }
    }

    // Prepare update/insert query
    if (($eAction == DBActions::SELECT)||($eAction == DBActions::SELECTONE)) {
      if (count($aFields) === 0) $sQuery = 'SELECT * FROM ' . self::$TABLE_STRUCTURES[$sTable]['table'];
      if (count($aFields) > 0) $sQuery = 'SELECT ' . implode(',', $aFields) . ' FROM ' . self::$TABLE_STRUCTURES[$sTable]['table'] ;
    } elseif ($eAction == DBActions::INSERT) {
      $sQuery = 'INSERT INTO ' . self::$TABLE_STRUCTURES[$sTable]['table'] . ' SET ' . $sUpsert;
    } elseif ($eAction == DBActions::UPDATE) {
      $sQuery = 'UPDATE ' . self::$TABLE_STRUCTURES[$sTable]['table'] . ' SET ' . $sUpsert;
    } elseif ($eAction == DBActions::DELETE) {
      $sQuery = 'DELETE FROM ' . self::$TABLE_STRUCTURES[$sTable]['table'];
    }
    if ( ($eAction == DBActions::SELECT)||($eAction == DBActions::SELECTONE)||($eAction == DBActions::UPDATE)||($eAction == DBActions::DELETE) ) {
      $sQuery .= ' WHERE ' . $sCondition;
      if (($eAction == DBActions::SELECT)&&( $sUpsert !== '')) $sQuery .= ' AND ' . $sUpsert;
      if (array_key_exists(self::$TABLE_STRUCTURES[$sTable]['primary_key'], $aRecord)) $sQuery .= ' AND ' . self::$TABLE_STRUCTURES[$sTable]['primary_key'] . '=' . $aRecord[self::$TABLE_STRUCTURES[$sTable]['primary_key']];
    }

    // Execute statement
    if (self::$DEBUG) error_log('SQL: ' . $sQuery);
    $oStmt = self::$MYSQLI->prepare($sQuery);

    if ( false===$oStmt ) {
      $REST_REPLY->setResult(null,-2,'ERR_DB_MYSQLI_PREPARE_ERROR[' . mysqli_errno(self::$MYSQLI) . ']');
      return $REST_REPLY->RESULT_CODE;
    }
    if (!$oStmt->execute()) {
      $REST_REPLY->setResult(null,-2,'ERR_DB_MYSQLI_EXECUTE_ERROR[' . mysqli_errno(self::$MYSQLI) . ']');
      return $REST_REPLY->RESULT_CODE;
    }

    $iNumAffectedRows = self::$MYSQLI->affected_rows;
    if (($eAction === DBActions::SELECT)||($eAction === DBActions::SELECTONE)) {
      $Result = $oStmt->get_result();
      if (!$Result) {
        $REST_REPLY->setResult(null,-2,'ERR_DB_MYSQLI_RESULT_ERROR[' . mysqli_errno(self::$MYSQLI) . ']');
        return $REST_REPLY->RESULT_CODE;
      }
      $iNumAffectedRows = $Result->num_rows;
      if (($eAction === DBActions::SELECTONE)&&($iNumAffectedRows>1)) {
        $REST_REPLY->setResult(null,-2,'ERR_DB_SELECTONE_TOOMANYRECORDS[' . $iNumAffectedRows . ']');
        return $REST_REPLY->RESULT_CODE;
      }

      $aResults = array();
      while ($rRecord = $Result->fetch_assoc()) {
        $aResults[] = $rRecord;
      }
      if ($eAction === DBActions::SELECT) {
        $REST_REPLY->appendData( array( $sTable => $aResults) );
      } else {
        if ($iNumAffectedRows>0) $aRecord = $aResults[0];
      }
    } elseif ($eAction === DBActions::INSERT) {
      $aRecord[self::$TABLE_STRUCTURES[$sTable]['primary_key']] = self::$MYSQLI->insert_id;
    }

    return $REST_REPLY->RESULT_CODE;
  }
}