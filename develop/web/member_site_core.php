<?php 

$database_server = "localhost";
$database_user = "root";
$database_password = "";
$database_name="mt_membersite";
$table = "membersite_members";
$user_readable_fields = "account,nick_name,email,image";
$user_updatable_fields = "account,nick_name,email,image";

require_once('member_site_core/basic.php');
require_once('member_site_core/lib.php');


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
					$hash[$key]= mysqli_real_escape_string( $db, $_POST[$key] );
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
		$id =  mysqli_real_escape_string( $db, $me['id'] );
		$sql = "UPDATE " . $table . " SET " . $sets . " WHERE ID = " . $id . ";";
		//TODO Transaction
		$res = mysqli_query($db,$sql);
		if ($res) {
			$sql = "SELECT * FROM " . $table . " WHERE ID = ". $id . ";";
			$res = mysqli_query($db,$sql);
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
	
	$condition = "account = '" . mysqli_real_escape_string( $db, $account ) . "' and ";
	$condition .= "passwd = '" . mysqli_real_escape_string( $db, $passwd  ) . "' and ";
	$condition .= "paused_at is NULL and deleted_at is NULL"; 
	
	$fields = getFields($db);
	
	$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
	$res = mysqli_query($db, $sql);
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