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

function search_confirm_key($key,$isExist=false) {
	global $table;
	global $table_keys;
	$db = db_open();

	$condition = "created_at IS NOT NULL and deleted_at IS NULL AND ";
	$condition .= "key_code = '" . mysqli_real_escape_string( $db , $key ) . "'";
	$sql = "SELECT * FROM " . $table_keys . " WHERE " . $condition . ";";
	$res = mysqli_query($db,$sql);
	if ($res) {
		$fields = getFields($db,$table_keys);
		$results = getDataFromRes($res,$fields);
		if (count( $results)>0) {
			$result = $results[0];
			$key_data = $result;
		} else {
			$key_data = null;
		}
	} else {
		$key_data = null;
	}

  if ($key_data != null) {
		if ($isExist) {
			$condition = "created_at IS NOT NULL and activated_at IS NOT NULL AND paused_at IS NULL and deleted_at IS NULL AND ";
		} else {
			$condition = "created_at IS NOT NULL and activated_at IS NULL AND paused_at is NULL and deleted_at IS NULL AND ";
		}
	  $condition .= "id = '" . mysqli_real_escape_string( $db , $key_data["member_id"] ) . "'";
  	$sql = "SELECT * FROM " . $table . " WHERE " . $condition . ";";
  	$res = mysqli_query($db,$sql);
  	if ($res) {
  		$fields = getFields($db,$table);
  		$results = getDataFromRes($res,$fields);
  		if (count( $results)>0) {
	  		$result = $results[0];
		  	$data = array( "status" => "OK" , "data" => $result , "key_data" => $key_data );
		  } else {
			  $data = array( "status" => "ERROR" );
	  	}
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
	global $table_keys;
	$result = search_confirm_key($key);
	if ($result["status"] == "OK") {
		$me = $result["data"];
		$key_data = $result["key_data"];
		$db = db_open();
		$activatedAt = new DateTime();
		$sets = "activated_at = '" . $activatedAt->format("Y-m-d H:i:s") . "'";
		$id = $me["id"];
		$sql = "UPDATE " . $table . " SET " . $sets . " WHERE ID = " . $id . ";";
		$res = mysqli_query($db, $sql);
		if ($res) {

			$sql = "SELECT * FROM " . $table . " WHERE ID = ". $id . ";";
			$res = mysqli_query($db, $sql);
			if ($res) {
				$fields = getFields($db,$table);
				$results = getDataFromRes($res,$fields);
				$member = filterUserData( $results[0] );

				$deletedAt = new DateTime();
	      $sets = "deleted_at = '" . $deletedAt->format("Y-m-d H:i:s") . "'";
				$sql = "UPDATE " . $table_keys . " SET " . $sets . " WHERE ID = " . $key_data["id"] . ";";
	      $res = mysqli_query($db, $sql);

				if ($res) {
					$data = array( "status" => "OK", "data"=> $member );
				} else {
					$data = array( "status" => "ERROR 4" ); // TODO Rollback
				}
			} else {
				$data = array( "status" => "ERROR 3" ); // TODO Rollback
			}
		} else {
			$data = array( "status" => "ERROR 2" ); // TODO Rollback
		}
		db_close($db);
	} else {
		$data = array( "status" => "ERROR 1" );
	}
	return $data;
}

?>
