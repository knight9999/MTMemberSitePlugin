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
  				$escapedValue = "'" . mysql_real_escape_string( $value , $db ) . "'";
  			}
  		}
  		array_push ( $escapedValuesList , $escapedValue );
  	}
  	
  	$values = implode( "," , $escapedValuesList );
  	$sql = "INSERT INTO " . $tableName . " (" . $fields . ") VALUES (" . $values . "); ";
  	
//  	print $sql . "\n";
  	mysql_query($sql,$db);
  		 
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
  
  $db = mysql_connect( "localhost", "root" , "");
  mysql_select_db( "mt_membersite");
  
  $fieldsList = array( "account" , "nick_name" , "email" , "passwd" , "created_at" );
  
  $members = getMembers();
  foreach ($members as $member) {
	  insertData($db,$fieldsList,$member);
  }
  
  mysql_close($db);



?>