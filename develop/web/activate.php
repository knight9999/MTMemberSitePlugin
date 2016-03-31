<?php 
// こういう風にPHPで直接実行するほか、Ajax経由のバージョンも作成する
// むしろ、Ajax経由のみで良い？
	require_once("member_site_core/action.php");
	$confirm_key = $_GET['key'];
    $result = search_confirm_key( $confirm_key );
    echo print_r( $result, true);
    echo "<br><br>-------<br>\n";
    $result = activate_by_confirm_key( $confirm_key );
    echo print_r( $result, true);
?>


<!DOCTYPE html>
<html lang="ja" itemscope itemtype="http://schema.org/Blog">
  <head>
    <meta charset="UTF-8">
    
    <title>SecondWebSite</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="member_site.js"></script>
  </head>
  <body>
  </body>
</html>


  