<!DOCTYPE html>
<html lang="ja" itemscope itemtype="http://schema.org/Blog">
  <head>
    <meta charset="UTF-8">
    
    <title>SecondWebSite</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
  </head>
  <body>
    <form>
      <label>Login</label>
      <input id="membersite_account" type="text" name="account">
      <input id="membersite_password" type="password" name="passwd">
      <button onclick="login(this); return false;">Login</button>
    </form>
    <button onclick="isLogined(); return false;">IsLogined</button><br />
    <button onclick="logout(); return false;">Logout</button>
    <script src="member_site.js"></script>
  </body>
</html>
