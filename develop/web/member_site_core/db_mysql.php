<?php

require_once('db_common.php');

class DbMySQL {

  public $database_server;
  public $database_user;
  public $database_password;
  public $database_name = "mt_membersite";

  public $db = null;
  public $count = 0;

  public function open() {
    if ($this->count == 0) {
      $this->db = mysqli_connect(
        $this->database_server,
        $this->databaser_user,
        $this->database_password
      );
      mysqli_select_db( $this->db, $this->database_name );
      $this->count = 1;
    } else {
      $this->count += 1;
    }
  }

  public function close() {
    if ($this->count == 0) {
      throw new Exception("DB error");
    }
    $this->count--;
    if ($this->count == 0) {
      mysqli_close($this->db);
    }
  }

  public function getFields($table) {
    $sql = "SHOW COLUMNS FROM " . $table . ";";
  	$res = mysqli_query($this->db, $sql);
  	$list = array();
  	if ($res) {
  		while ($row = mysqli_fetch_assoc($res)) {
  			$list[$row['Field']] = $row['Type'];
  		}
  	}
  	return $list;
  }

  public function getDataFromRes($res,$fields) {
  	$results = array();
  	if ($res) {
  		while ($row = mysqli_fetch_assoc($res)) {
  			reset($row);
  			$result = array();
  			foreach ($fields as $key => $type) {
  				$value = $row[$key];
  				$result[$key] = $value;
  			}
  			array_push( $results , $result );
  		}
  	}
  	return $results;
  }

  public function escapedFieldsAndValues( $hash ) {
  	$escapedFieldsList = array();
  	$escapedValuesList = array();
  	foreach ($hash as $key => $value) {
  		$escapedField = mysqli_real_escape_string( $this->db , $key );
  		if (is_null($value)) {
  			$escapedValue = "NULL";
  		} else {
  			if ($value instanceof DateTime) {
  				$escapedValue = "'" . $value->format("Y-m-d H:i:s") . "'";
  			} else if (is_string($value)){
  				$escapedValue = "'" . mysqli_real_escape_string( $this->db, $value ) . "'";
  			} else {
  				$escapedValue = $value;
  			}
  		}
  		array_push( $escapedFieldsList , $escapedField );
  		array_push( $escapedValuesList , $escapedValue );
    }
  	$fields = implode( ",", $escapedFieldsList );
  	$values = implode( ",", $escapedValuesList );
  	return array($fields,$values);
  }
  
}

?>
