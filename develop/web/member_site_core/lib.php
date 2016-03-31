<?php 

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

function search_confirm_key($key) {
	global $table;
	$db = db_open();
	
	$condition = "created_at IS NOT NULL and activated_at IS NULL AND paused_at is NULL and deleted_at IS NULL AND ";
	$condition .= "confirm_key = '" . mysqli_real_escape_string( $db , $key ) . "'";
	$sql = "SELECT * FROM " . $table . " WHERE " . $condition . ";";
	$res = mysqli_query($db,$sql);
	if ($res) {
		$fields = getFields($db);
		$results = getUserDataFromRes($res,$fields);
		if (count( $results)>0) {
			$result = $results[0];
			$data = array( "status" => "OK" , "data" => $result );
		} else {
			$data = array( "status" => "ERROR" );
		}	
	} else {
		$data = array( "status" => "ERROR" );
	} 
	db_close($db);
	return $data;
}

function activate_by_confirm_key($key) {
	global $table;
	$result = search_confirm_key($key);
	if ($result["status"] == "OK") {
		$me = $result["data"];
		$db = db_open();
		$activatedAt = new DateTime();
		$sets = "activated_at = '" . $activatedAt->format("Y-m-d H:i:s") . "' , confirm_key = NULL";
		$id = $me["id"];
		$sql = "UPDATE " . $table . " SET " . $sets . " WHERE ID = " . $id . ";";
		$res = mysqli_query($db, $sql);
		if ($res) {
			
			$sql = "SELECT * FROM " . $table . " WHERE ID = ". $id . ";";
			$res = mysqli_query($db, $sql);
			if ($res) {
				$fields = getFields($db);
				$results = getUserDataFromRes($res,$fields);
				$data = array( "status" => "OK", "data"=> filterUserData( $results[0] ) );
			} else {
				$data = array( "status" => "OK" );
			}
		} else {
			$data = array( "status" => "ERROR" );
		}
		db_close($db);
	} else {
		$data = array( "status" => "ERROR" );
	}
	return $data;
}

?>