<!DOCTYPE html>
<html lang="ja" itemscope itemtype="http://schema.org/Blog">
  <head>
    <meta charset="UTF-8">
    
    <title>SecondWebSite</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
  </head>
  <body>
  <?php require_once("member_site_action.php"); ?>
    <div id="login">
  <?php if (isLogined()): ?>
  	Logined : 
  	<?php echo me("account"); ?>,
  	<?php echo me("nick_name"); ?>
  <?php else: ?>
  	Logout
  <?php endif ?>
    </div>
    <div id="me"></div>
    <form>
      <label>Login</label>
      <input id="membersite_account" type="text" name="account">
      <input id="membersite_password" type="password" name="passwd">
      <button onclick="login(this); return false;">Login</button>
    </form>
    <button onclick="getLatestMe(); return false;">getLatestMe</button><br />
    <button onclick="showMe(); return false;">showMe</button><br />
    <button onclick="logout(); return false;">Logout</button>
    <script src="member_site.js"></script>
    
    <script>
	  function login(btn) {
	    var form = btn.form;
		var account = form["account"].value;
		var password = form["passwd"].value;
		memberSite.login(account,password,
			"",
			function(err) { alert( JSON.stringify(err) ); }
		);
	  }   

	  function getLatestMe() {
	    memberSite.getMe(
		  function(data) { alert( JSON.stringify(data) ); },
		  function(err) { alert( JSON.stringify(err) ); }
		);
	  }   
	  
      function showMe() {
          var me = memberSite.me;
          if (me) {
              alert( JSON.stringify( me ) );
          } else {
              alert( "You are not logged in" );
          }
      }

      function logout() {
	    memberSite.logout( "", "");
      }          
    </script>
  </body>
</html>
