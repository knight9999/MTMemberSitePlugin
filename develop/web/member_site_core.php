<?php 

$database_server = "localhost";
$database_user = "root";
$database_password = "";
$database_name="mt_membersite";
$table = "membersite_members";
$user_readable_fields = "account,nick_name,email,image";
$user_updatable_fields = "account,nick_name,email,image";


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

// PHP Lib

function isLogined() {
	$me = me();
	if ($me) { 
		return true; 
	}
	return false;
}

function me($key = null) {
	session_start();
	if (isset($_SESSION['me'])) {
		$me = $_SESSION["me"];
	} else {
		$me = null;
	}
	session_write_close();
	if ($key != null) {
		return $me[$key];
	}
	return $me;
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

// JavaScript API

function dispatchAction() {
	$action = $_POST["action"];
	if ($action == "login") {
		return loginAction();
	} else if ($action == "me") {
		return meAction();
	} else if ($action == "logout") {
		return logoutAction();
	} else if ($action == "edit") {
		return editAction();
	}
}


function meAction() {
	$me = me();
		
	if ($me) {
		$data = array( "status" => "OK" ,
			"message" => "PHP Task is finished",
	    	"data" => filterUserData( $me ) );
	} else {
		$data = array( "status" => "ERROR" ,
				"code" => 101,
				"message" => "There is no user data or multiple data" );
		
	}
	return $data;
}

function logoutAction() {
	$me = me();
	if ($me) {
		session_start();
		unset( $_SESSION["me"] );
		session_write_close();
		$data = array( "status" => "OK" ,
			"message" => "do Logout");
	} else {
		$data = array( "status" => "ERROR",
			"code" => 102,
			"message" => "you are not logined");
	}
	return $data;
}

function editAction() {
	global $table;
	global $user_updatable_fields;
	
	$me = me();
	
	if ($me) {	
		$db = db_open();
	
		$fields = getFields($db);
		
		$list = explode(",",$user_updatable_fields);
		$hash = array();
		foreach ($list as $key) {
			if (isset( $_POST[$key] ) ) {
				if ($_POST[$key] == null) {
					$hash[$key] = "NULL";
				} else {
					$hash[$key]= mysql_real_escape_string( $_POST[$key] );
				}
			}
		}	
		$setList = array();
		foreach( $hash as $key => $_val ) {
			if ($fields[$key] == "text") {
				$val = "'" . $_val . "'";
			} else {
				$val = $_val;
			}
			array_push($setList , $key . "=" . $val );
		}	
		$sets = implode( ',' , $setList );
		$id =  mysql_real_escape_string( $me['id'] );
		$sql = "UPDATE " . $table . " SET " . $sets . " WHERE ID = " . $id . ";";
		//TODO Transaction
		$res = mysql_query($sql,$db);
		if ($res) {
			$sql = "SELECT * FROM " . $table . " WHERE ID = ". $id . ";";
			$res = mysql_query($sql,$db);
			if ($res) {
				$results = getUserDataFromRes($res,$fields);
 				session_start();
 				$_SESSION["me"] = $results[0];
 				session_write_close();
				$data = array( "status" => "OK", "data"=> filterUserData( $results[0] ) );
			} else {
				$data = array( "status" => "ERROR",
						"code" => 103,
						"message" => "you are not logined");
			}
		} else {
			$data = array( "status" => "ERROR",
					"code" => 103,
					"message" => "you are not logined");
		}	
		db_close($db);
	} else {
		$data = array( "status" => "ERROR",
				"code" => 104,
				"message" => "you are not logined");
	}
	return $data;
}

function loginAction() {
	global $table;
	
	$db = db_open();
	
	$account = $_POST["account"];
	$password = $_POST["password"];
	$passwd = encryptPassword($password);	
	
	$condition = "account = '" . mysql_real_escape_string( $account , $db ) . "' and ";
	$condition .= "passwd = '" . mysql_real_escape_string( $passwd , $db ) . "' and ";
	$condition .= "paused_at is NULL and deleted_at is NULL"; 
	
	$fields = getFields($db);
	
	$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
	$res = mysql_query($sql,$db);
	if ($res) {
		$results = getUserDataFromRes($res,$fields);
	 	if (count($results) == 1) {
 			session_start();
 			$_SESSION["me"] = $results[0];
 			session_write_close();
		
			$data = array( "status" => "OK" ,
				"message" => "PHP Task is finished",
		    	"data" => filterUserData( $results[0] ) );
 		} else {
 			$data = array( "status" => "ERROR" ,
 				"code" => 101,
 				"message" => "There is no user data or multiple data" );
		
 		}
	} else {
		$data = array( "status" => "ERROR" ,
				"code" => 101,
				"message" => "There is no user data or multiple data" );
	}
	db_close($db);
	
	return $data;
	
}


?>