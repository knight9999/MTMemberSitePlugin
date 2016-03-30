<!DOCTYPE html>
<html lang="ja" itemscope itemtype="http://schema.org/Blog">
  <head>
    <meta charset="UTF-8">
    
    <title>SecondWebSite</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <script src="member_site.js"></script>
  </head>
  <body>
  <?php require_once("member_site_core/action.php"); ?>
    <div id="login">
  <?php if (isLogined()): ?>
  	Logined : 
  	<?php echo me("account"); ?>,
  	<?php echo me("nick_name"); ?>
  	&nbsp;
  	<a href="index.php">index.php</a>
  <?php else: ?>
  	You are not logged in. Please go back to <a href="index.php">index.php</a>
  <?php endif ?>
    </div>
    <form>
    	<label>Signup</label>
    	<dt>Account</dt>
    	<dd>
    		<input type="text" name="account">
    	</dd>
    	<dt>Password</dt>
    	<dd>
    		<input type="password" name="password">
    	</dd>
    	<dt>Password confirm</dt>
    	<dd>
    		<input type="password" name="password_confirm">
    	</dd>
    	<dt>NickName</dt>
    	<dd>
	    	<input type="text" name="nick_name">
 		</dd>
 		<dt>email</dt>
 		<dd>
	    	<input type="text" name="email">
	    </dd>
    	<button onclick="signup(this); return false;">Signup</button>
    </form>
    <script>
		function signup(btn) {
			var form = btn.form;
			var account = form["account"].value;
			var password = form["password"].value;
			var nick_name = form["nick_name"].value;
			var email = form["email"].value;
			memberSite.signup( { account : account , password : password, nick_name : nick_name , email : email } ,
					function(data) { alert( "1:" + JSON.stringify(data) ); },
					function(err) { alert( JSON.stringify(err) ); }
				); 
}
    </script>
  </body>
</html>
    