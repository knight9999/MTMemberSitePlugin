<?php 
	require_once("member_site_core/action.php");
	$confirm_key = $_GET['key'];
    $result = search_confirm_key( $confirm_key );
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
    <form>
    	<label>Reminder</label>
    	<dt>Password</dt>
    	<dd>
    		<input type="password" name="password">
    	</dd>
    	<dt>Password confirm</dt>
    	<dd>
    		<input type="password" name="password_confirm">
    	</dd>
    	<br><br>
    	<div>
    	<button onclick="repassword(this); return false;">RePassword</button>
    	</div>
    </form>
    <div id="link"></div>
    <script>
		function repassword(btn) {
			var form = btn.form;
			var password = form["password"].value;
			memberSite.repassword( {  password : password, key : '<?php echo $confirm_key ?>' } ,
					function(data) { 
						alert( JSON.stringify(data) ); 
					},
					function(err) { alert( JSON.stringify(err) ); }
				); 
			}
    </script>
  </body>
</html>
    