<?php 

//   $account = $_POST["account"];
//   $password = $_POST["password"];

//   $data = array( "status" => "OK" , 
//                  "message" => "PHP Task is finished");

  require_once('member_site_core.php');

  $data = dispatchAction();
  
  echo json_encode( $data );

?>