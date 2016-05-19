<?php
	require_once("member_site_core/action.php");
	$confirm_key = $_GET['key'];
  $result = search_confirm_key( $confirm_key , true);
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
    	<dt>new Password</dt>
    	<dd>
    		<input type="password" name="password">
    	</dd>
    	<dt>Password confirm</dt>
    	<dd>
    		<input type="password" name="password_confirm">
    	</dd>
    	<br><br>
    	<div>
    	<button onclick="reminderEdit(this); return false;">ReminderEdit</button>
    	</div>
    </form>
    <div id="link"></div>
    <script>
		function reminderEdit(btn) {
			var form = btn.form;
			var password = form["password"].value;
			memberSite.reminderEdit( {  password : password, key : '<?php echo $confirm_key ?>' } ,
					function(data) {
						alert( JSON.stringify(data) );
					},
					function(err) { alert( JSON.stringify(err) ); }
				);
			}
    </script>
  </body>
</html>
