<?php 

// Basic

function db_open() {
	global  $database_server , $database_user , $database_password;
	$db = mysql_connect( $database_server , $database_user , $database_password );
	mysql_select_db( "mt_membersite",$db);
	return $db;
}

function db_close($db) {
	mysql_close($db);
}

function getFields($db) {
	global $table;
	$sql = "SHOW COLUMNS FROM " . $table . ";";
	$res = mysql_query($sql,$db);
	$list = array();
	if ($res) {
		while ($row = mysql_fetch_assoc($res)) {
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

function getUserDataFromRes($res,$fields) {
	$results = array();
	if ($res) {
		while ($row = mysql_fetch_assoc($res)) {
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

?>