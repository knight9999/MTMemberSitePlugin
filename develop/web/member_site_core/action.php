<?php 

//   $account = $_POST["account"];
//   $password = $_POST["password"];

//   $data = array( "status" => "OK" , 
//                  "message" => "PHP Task is finished");
require_once('core.php');

if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) { // direct call

  $data = dispatchAction();
  
  echo json_encode( $data );
}

?>