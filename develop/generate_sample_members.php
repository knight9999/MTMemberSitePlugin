<?php 

  function insertData( $db , $fieldsList , $valuesList ) {
  	$tableName = "membersite_members";
  	
  	$fields = implode( "," , $fieldsList );
  	$escapedValuesList = array();
  	foreach( $valuesList as $value) {
  		if (is_null($value)) {
  			$escapedValue = "NULL";
  		} else {
  			if ($value instanceof DateTime) {
  				$escapedValue = "'" . $value->format("Y-m-d H:i:s") . "'";
  			} else {
  				$escapedValue = "'" . mysqli_real_escape_string( $db, $value ) . "'";
  			}
  		}
  		array_push ( $escapedValuesList , $escapedValue );
  	}
  	
  	$values = implode( "," , $escapedValuesList );
  	$sql = "INSERT INTO " . $tableName . " (" . $fields . ") VALUES (" . $values . "); ";
  	
//  	print $sql . "\n";
  	mysqli_query($db,$sql);
  		 
  }

  function encryptPassword($password) {
  	$pwKey = "PASSKEY";
  	$pass1 = sha1($password);
  	$passw = $pwKey . $pass1 . $pwKey;
  	$passwd = sha1($passw);
  	return $passwd;
  }

  function getMembers() {
  	return array(
  		array( "member" , "Member" , "member@example.com" , encryptPassword("hogehoge") , new DateTime("2016/3/28 10:00:00") ),
  		array( "taro" , "Taro" , "taro@example.com" , encryptPassword("tarotaro") , new DateTime("2016/3/28 11:00:00") )
  	);
  }
  
  $db = mysqli_connect( "localhost", "root" , "");
  mysqli_select_db( $db, "mt_membersite" );
  
  $fieldsList = array( "account" , "nick_name" , "email" , "passwd" , "created_at" );
  
  $members = getMembers();
  foreach ($members as $member) {
	  insertData($db,$fieldsList,$member);
  }
  
  mysqli_close($db);



?>