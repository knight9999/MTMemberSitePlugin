<?php

$database_server = "localhost";
$database_user = "root";
$database_password = "";
$database_name="mt_membersite";
$table = "membersite_members";
$table_keys = "membersite_keys";
$user_readable_fields = "account,nick_name,email,image";
$user_updatable_fields = "account,nick_name,email,image";
$user_signup_fields = "account,password,nick_name,email";
$user_signup_without_mail = false;
$mail_from = "knaito@asial.co.jp";

$user_signup_validations = array(
	"account" => array( "Unique" , "Required" , array( "Length>=" , 4 ) , array( "Length<=" , 20 ) ),
	"password" => array( "Required" , array( "Length>=" , 4) , array( "Length<=" , 20 ) ),
	"nick_name" => array( "Required" , array( "Length>=" , 4) , array( "Length<=" , 20 )),
	"email" => array( "Unique", "Required" , "EmailFormat" )
);


require_once('basic.php');
require_once('lib.php');


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
	} else if ($action == "signup") {
		return signupAction();
	} else if ($action == "reminder") {
		return reminderAction();
	} else if ($action == "reminder_edit") {
		return reminderEditAction();
	}
	$data = array( "status" => "ERROR",
			"code" => 110,
			"message" => "No such action");
	return $data;
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

		$fields = getFields($db,$table);

		$userdata = $_POST["data"];
		$list = explode(",",$user_updatable_fields);
		$hash = array();
		foreach ($list as $key) {
			if (isset( $userdata[$key] ) ) {
				if ($userdata[$key] == null) {
					$hash[$key] = "NULL";
				} else {
					$hash[$key]= mysqli_real_escape_string( $db , $userdata[$key] );
				}
			}
		}	//TODO バリデーション
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
		$res = mysqli_query($db, $sql);
		if ($res) {
			$sql = "SELECT * FROM " . $table . " WHERE ID = ". $id . ";";
			$res = mysqli_query($db, $sql);
			if ($res) {
				$results = getDataFromRes($res,$fields);
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

	$condition = "account = '" . mysqli_real_escape_string( $db , $account ) . "' and ";
	$condition .= "passwd = '" . mysqli_real_escape_string( $db , $passwd ) . "' and ";
	$condition .= "activated_at IS NOT NULL AND ";
	$condition .= "paused_at is NULL and deleted_at is NULL";

	$fields = getFields($db,$table);

	$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
	$res = mysqli_query($db, $sql);
	if ($res) {
		$results = getDataFromRes($res,$fields);
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

function addError( $errors , $key , $error ) {
	if (isset( $errors[$key] )) {
		array_push( $errors[$key] , $error );
	} else {
		$errors[$key] = array( $error );
	}
	return $errors;
}

function reminderAction() {
	global $table;
	global $table_keys;
	$email = $_POST["email"];

	$db = db_open();
	$fields = getFields($db,$table);

	$condition = " email = '" .  mysqli_real_escape_string( $db , $email ) . "' AND ";
	$condition .= " activated_at IS NOT NULL AND ";
	$condition .= " paused_at is NULL and deleted_at is NULL ";
	$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
	$res = mysqli_query($db,$sql);
	if ($res) {
		$results = getDataFromRes($res,$fields);
		if (count($results)>0) {
			$member = $results[0];
			$member_id = $member["id"];
			$key_code = sha1( rand(1,1000000) );

			$hash_key = array();
			$hash_key["member_id"] = $member_id;
			$hash_key["created_at"] = new DateTime();
			$hash_key["key_type"] = 2;
			$hash_key["key_code"] = $key_code;

			list($fields,$values) = escapedFieldsAndValues( $db, $hash_key );
	  	$sql = "INSERT INTO " . $table_keys . " (" . $fields . ") VALUES (" . $values . "); ";

			$res = mysqli_query($db, $sql);
			if ($res) {
				$hash = array();
				$hash['email'] = $email;
  			$hash["title"] = "Reminder Mail";
				$text = <<<EOT
  	This is a test \n You confirm key = $key_code .
	<a href="http://mt101.local/web/reminder.php?key=$key_code">Reminder</a>
EOT;
				$hash["text"] = $text;

				mailTo($hash);
				$data = array( "status" => "OK" ,
						"result" => "OK" ,
						"data" => array( )
				);

			} else {
				$data = array( "status" => "ERROR" ,
						"code" => 104,
						"message" => "No user data" );
			}
		} else {
			$data = array( "status" => "ERROR" ,
					"code" => 104,
					"message" => "No user data" );
		}
	} else {
		$data = array( "status" => "ERROR" ,
				"code" => 104,
				"message" => "No user data" );
	}
	db_close($db);

	return $data;
}

function reminderEditAction() {
	global $table;
	global $table_keys;
	$key_code = $_POST["key"];
	$password = $_POST["password"];
	$db = db_open();
	$fields = getFields($db,$table);
	$field_keys = getFields($db,$table_keys);

	$condition = " key_code = '" .  mysqli_real_escape_string( $db , $key_code ) . "' AND ";
	$condition .= " deleted_at is NULL ";
	$sql = "SELECT * FROM " . $table_keys . " WHERE ".$condition . ";";
	$res = mysqli_query($db,$sql);
	if ($res) {
		$results = getDataFromRes($res,$field_keys);
		if (count($results)>0) {
      $data_key = $results[0];
			$key_id = $data_key["id"];
			$member_id = $data_key["member_id"];
      $condition = " id = " . $member_id . " AND activated_at IS NOT NULL AND ";
      $condition .= " paused_at IS NULL AND deleted_at IS NULL";
			$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
			$res = mysqli_query($db,$sql);
			if ($res) {
        $results = getDataFromRes($res,$fields);
        if (count($results)>0) {
  				$me = $results[0];
	    		$id = $me["id"];
  	  		$passwd = encryptPassword($password);

	  	  	$sets = "passwd = '" . mysqli_real_escape_string( $db , $passwd ) . "'";
    			$sql = "UPDATE " . $table . " SET " . $sets . " WHERE id = " . $id . ";";
  	  		$res = mysqli_query($db, $sql);
	  	  	if ($res) {

						$deletedAt = new DateTime();
						$sets = "deleted_at = '" . $deletedAt->format("Y-m-d H:i:s") . "'";
						$sql = "UPDATE " . $table_keys . " SET " . $sets . " WHERE id = " . $key_id . ";";
            $res = mysqli_query($db, $sql);
						if ($res) {

							$deletedAt = new DateTime();
				      $sets = "deleted_at = '" . $deletedAt->format("Y-m-d H:i:s") . "'";
							$sql = "UPDATE " . $table_keys . " SET " . $sets . " WHERE ID = " . $key_id . ";";
				      $res = mysqli_query($db, $sql);


	  			  	$data = array( "status" => "OK" ,
			 		  		"result" => "OK" ,
						  	"data" => array( )
			  		  );
						} else {
							$data = array( "status" => "ERROR" ,
						  		"code" => 104,
							  	"message" => "No user data" );
						}
          }
		  	} else {
			  	$data = array( "status" => "ERROR" ,
				  		"code" => 104,
					  	"message" => "No user data" );
			  }
		  } else {
				$data = array( "status" => "ERROR" ,
						"code" => 104,
						"message" => "No user data" );
			}
		} else {
			$data = array( "status" => "ERROR" ,
				"code" => 104,
				"message" => "No user data" );
		}
	} else {
		$data = array( "status" => "ERROR" ,
				"code" => 104,
				"message" => "No user data" );
	}
	db_close($db);

	return $data;
}

function signupAction() { // TODO バリデーションだけの処理を分離すべき
	global $table;
	global $table_keys;
	global $user_signup_fields;
	global $user_signup_validations;
	global $user_signup_without_mail;

	$db = db_open();

	$userdata = $_POST["data"];

	$list = explode(",",$user_signup_fields);
	$hash = array();
	foreach ($list as $key) {
		if (isset( $userdata[$key] ) ) {
			if ($userdata[$key] == null) {
				$hash[$key] = null;
			} else {
				$hash[$key]= $userdata[$key];
			}
		}
	}

	$errors = array();
	foreach ($list as $key) {
		if (isset($hash[$key])) {
			$value = $hash[$key];
		} else {
			$value = null;
		}

		if (isset( $user_signup_validations[$key] ) ) {
			$validations = $user_signup_validations[$key];
			foreach ($validations as $validation) {
				if (is_string($validation)) {
					if ($validation == "Required") {
						if ($value == null || $value == "") {
							$errors = addError( $errors , $key , array( "code"=>1000, "message" => "Required" ) );
						}
					} else if ($validation == "Unique") {
						if ($value != null) {
							$db = db_open();
							$fields = getFields($db,$table);

							// $keyのタイプがstringのときのみ、この処理 TODO 文字列以外に数値の場合の処理も実装する
							$condition = mysqli_real_escape_string($db,$key) . " = '" .  mysqli_real_escape_string( $db , $value ) . "' AND ";
							$condition .= " activated_at IS NOT NULL AND ";
							$condition .= " paused_at is NULL and deleted_at is NULL ";
							$sql = "SELECT * FROM " . $table . " WHERE ".$condition . ";";
							$res = mysqli_query($db,$sql);
							if ($res) {
								$results = getDataFromRes($res,$fields);
								if (count($results)>0) {
									$errors = addError( $errors , $key , array( "code"=>1003 , "message"=>"Unique" ) );
								}
							}
							db_close($db);
						}
					}
				} else {
					if ($validation[0] == "Length<=") {
						$length = $validation[1];
						if ($value != null && strlen( $value ) > $length ) {
							$errors = addError( $errors , $key , array( "code"=>1001, "params" => array( $length ), "message" => "length<="  ) );
						}
					} else if ($validation[0] == "Length>=") {
						$length = $validation[1];
						if ($value != null && strlen( $value ) < $length ) {
							$errors = addError( $errors , $key , array( "code"=>1002, "params" => array( $length ), "message" => "length>="  ) );
						}
					}
				}
			}
		}
	}

	if (count($errors)>0) {
		$data = array( "status" => "OK" ,
			"data" => array( "result" => "validation_error" , "data" => $errors )
		);
	} else {
		$db = db_open();
		$escapedFieldsList = array();
		$escapedValuesList = array();
		$hash["created_at"] = new DateTime();

		if ($user_signup_without_mail) {
			$hash["activated_at"] = new DateTime();
		} else {
//			$hash["confirm_key"] = sha1( rand(1,1000000) );
		}
		$password = $hash["password"];
		unset( $hash["password"] );
		$passwd = encryptPassword($password);
		$hash["passwd"] = $passwd;

		// foreach( $hash as $key => $value) {
		// 	$escapedField = mysqli_real_escape_string( $db , $key );
  	//   if (is_null($value)) {
  	// 		$escapedValue = "NULL";
  	// 	} else {
  	// 		if ($value instanceof DateTime) {
  	// 			$escapedValue = "'" . $value->format("Y-m-d H:i:s") . "'";
  	// 		} else {
  	// 			$escapedValue = "'" . mysqli_real_escape_string( $db, $value ) . "'";
  	// 		}
  	// 	}
  	// 	array_push( $escapedFieldsList , $escapedField );
  	// 	array_push( $escapedValuesList , $escapedValue );
  	// }
  	// $fields = implode( "," , $escapedFieldsList );
  	// $values = implode( "," , $escapedValuesList );
		list ($fields,$values) = escapedFieldsAndValues($db, $hash);
  	$sql = "INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . "); ";
  	$res = mysqli_query($db,$sql);
  	if ($res) {
			$escapedFieldsList = array();
			$escapedValuesList = array();
			$hash_key = array();
			$hash_key["member_id"] = mysqli_insert_id($db);
			$hash_key["created_at"] = $hash["created_at"];
			$hash_key["key_type"] = 1;
			$hash_key["key_code"] = sha1( rand(1,1000000) );
			// foreach( $hash_key as $key => $value) {
			// 	$escapedField = mysqli_real_escape_string( $db , $key );
	  	//   if (is_null($value)) {
	  	// 		$escapedValue = "NULL";
	  	// 	} else {
	  	// 		if ($value instanceof DateTime) {
	  	// 			$escapedValue = "'" . $value->format("Y-m-d H:i:s") . "'";
	  	// 		} else if (is_string($value)){
	  	// 			$escapedValue = "'" . mysqli_real_escape_string( $db, $value ) . "'";
	  	// 		} else {
			// 			$escapedValue = $value;
			// 		}
	  	// 	}
	  	// 	array_push( $escapedFieldsList , $escapedField );
	  	// 	array_push( $escapedValuesList , $escapedValue );
	  	// }
			// $fields = implode( "," , $escapedFieldsList );
	  	// $values = implode( "," , $escapedValuesList );
			list($fields,$values) = escapedFieldsAndValues( $db, $hash_key );

	  	$sql = "INSERT INTO " . $table_keys . " (" . $fields . ") VALUES (" . $values . "); ";
	  	$res = mysqli_query($db,$sql);
      if ($res) {
  			$confirm_key = $hash_key['key_code'];
     		$text = <<<EOT
   	This is a test \n You confirm key = $confirm_key .
	<a href="http://mt101.local/web/activate.php?key=$confirm_key">Activate</a>
EOT;
  			$hash["title"] = "Activation Mail";
  			$hash["text"] = $text;
        $hash["confirm_key"] = $confirm_key;
				error_log(print_r($hash,true));
  			mailTo($hash);
  			$data = array( "status" => "OK" ,
  					"result" => "OK" ,
  					"data" => array( )
  					);
			} else {
				$data = array( "status" => "ERROR" ,
	  				"code" => 101,
	  				"message" => "Insert data failed" );
			}
  	} else {
  		$data = array( "status" => "ERROR" ,
  				"code" => 101,
  				"message" => "Insert data failed" );
  	}
  	db_close($db);

	}
	return $data;

}

function mailTo($hash) {
	global $mail_from;
	$title = $hash["title"];
	$text = $hash["text"];

	mb_send_mail($hash['email'], $title , $text , "From: ". $mail_from );

}

?>
