<?php

class NotNull {

};

// Basic

function db_open() {
	global  $database_server , $database_user , $database_password;
	$db = mysqli_connect( $database_server , $database_user , $database_password );
	mysqli_select_db( $db, "mt_membersite");
	return $db;
}

function db_close($db) {
	mysqli_close($db);
}

function getFields($db,$table) {
	$sql = "SHOW COLUMNS FROM " . $table . ";";
	$res = mysqli_query($db, $sql);
	$list = array();
	if ($res) {
		while ($row = mysqli_fetch_assoc($res)) {
			$list[$row['Field']] = $row['Type'];
		}
	}
	return $list;
}

function encryptPassword($password) {
	$pwKey = "PASSKEY";
	$pass1 = sha1($password);
	$passw = $pwKey . $pass1 . $pwKey;
	$passwd = sha1($passw);
	return $passwd;
}

function filterUserData($userData) {
	global $user_readable_fields;
	$result = array();
	$hash = explode(",",$user_readable_fields);
	foreach( $hash as $key) {
		if (isset( $userData[$key]) ) {
			$result[$key] = $userData[$key];
		}
	}
	return $result;
}

function getDataFromRes($res,$fields) {
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

function escapedFieldsAndValues( $db , $hash ) {
	$escapedFieldsList = array();
	$escapedValuesList = array();
	foreach ($hash as $key => $value) {
		$escapedField = mysqli_real_escape_string( $db , $key );
		if (is_null($value)) {
			$escapedValue = "NULL";
		} else {
			if ($value instanceof DateTime) {
				$escapedValue = "'" . $value->format("Y-m-d H:i:s") . "'";
			} else if (is_string($value)){
				$escapedValue = "'" . mysqli_real_escape_string( $db, $value ) . "'";
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

?>
