<?php 

$database_server = "localhost";
$database_user = "root";
$database_password = "";
$database_name="mt_membersite";
$table = "membersite_members";
$user_readable_fields = "";
$user_updatable_fields = "";


function dispatchAction() {
	$action = $_POST["action"];
	if ($action == "login") {
		return loginAction();
	} else if ($action == "me") {
		return meAction();
	} else if ($action == "logout") {
		return logoutAction();
	}
}

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

function meAction() {
	session_start();
	$me = $_SESSION["me"];
	session_write_close();
	
	if ($me) {
		$data = array( "status" => "OK" ,
			"message" => "PHP Task is finished",
	    	"data" => $me );
	} else {
		$data = array( "status" => "ERROR" ,
				"code" => 101,
				"message" => "There is no user data or multiple data" );
		
	}
	return $data;
}

function logoutAction() {
	session_start();
	$me = $_SESSION["me"];
	if ($me) {
		unset( $_SESSION["me"] );
		$data = array( "status" => "OK" ,
			"message" => "do Logout");
	} else {
		$data = array( "status" => "ERROR",
			"message" => "you are not logined");
	}
	session_write_close();
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
		
	db_close($db);
	
 	if (count($results) == 1) {
 		session_start();
 		$_SESSION["me"] = $results[0];
 		session_write_close();
		
		$data = array( "status" => "OK" ,
			"message" => "PHP Task is finished",
	    	"data" => $results[0] );
 	} else {
 		$data = array( "status" => "ERROR" ,
 				"code" => 101,
 				"message" => "There is no user data or multiple data" );
		
 	}
	return $data;
	
}


?>