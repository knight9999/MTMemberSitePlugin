function login(btn) {
  var form = btn.form;
  var account = form["account"].value;
  var password = form["passwd"].value;
  ajax_post({ 
	 url : "member_site_action.php",
	 data: { action : "login" , account : account , password : password } ,
	 success : function($data) {
		 console.log( JSON.stringify( $data ) );
		 alert( JSON.stringify( $data ) );
	 },
	 failure : function(err) {
		 alert("ERROR");
	 }
  });
}

function isLogined() {
	  ajax_post({ 
			 url : "member_site_action.php",
			 data: { action : "me" } ,
			 success : function($data) {
				 console.log( JSON.stringify( $data ) );
				 alert( JSON.stringify( $data ) );
			 },
			 failure : function(err) {
				 alert("ERROR");
			 }
		  });
}

function logout() {
	  ajax_post({ 
			 url : "member_site_action.php",
			 data: { action : "logout" } ,
			 success : function($data) {
				 console.log( JSON.stringify( $data ) );
				 alert( JSON.stringify( $data ) );
			 },
			 failure : function(err) {
				 alert("ERROR");
			 }
		  });
}


function ajax_post(options) {
    var url = options.url;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
        if (xhr.status==0) {
          if (options.fail) {
            options.failure( xhr.responseText );
          }
        } else {
          if ( (200 <= xhr.status && xhr.status < 300) || xhr.status == 304 ) {
            if (options.success) {
              options.success( JSON.parse(xhr.responseText) ); // 戻り値はJSONと決め打ち
            }
          } 
        }
      }
    }
    xhr.open( "POST" , url );
    xhr.setRequestHeader("content-type","application/x-www-form-urlencoded;charset=UTF-8");
    var querys = [];
    if (options.data != null) {
      for (var key in options.data) {
        var val = options.data[key];
        querys.push( key + "=" + encodeURIComponent(val) );
      }
    }
    var query = querys.join('&');
    xhr.send( query );
}
