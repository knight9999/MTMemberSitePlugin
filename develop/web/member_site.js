
function MemberSite() { 
	this.baseUrl = "";		
	this.me = null;
}

MemberSite.prototype.apiUrl = function(url) {
	return this.baseUrl + url;
};

MemberSite.prototype.guard = function(success) {
	if (success == null) {
		success = function() { };
	} else if (typeof success == "string") {
		var url = success;
		success = function() {
			location.href = url;
		}
	}
	return success;
};

MemberSite.prototype.login = function(account, password, success, failure) {
	success = this.guard(success);
	failure = this.guard(failure);
	var self = this;
	this.ajax_post({ 
		url : this.apiUrl( "member_site_action.php" ),
		data: { action : "login" , account : account , password : password } ,
		success : function(data) {
//			console.log( JSON.stringify( data ) );
			if (data.status == "ERROR") {
				failure( data );
			} else {
				self.me = data.data;
				success( data.data );
			}
		},
		failure : function(err) {
			failure( { code : 100 , message : err } );
		}
	});
};

MemberSite.prototype.getMe = function(success, failure) {
	success = this.guard(success);
	failure = this.guard(failure);
	var self = this;
	this.ajax_post({ 
		url : this.apiUrl( "member_site_action.php" ),
		data: { action : "me" } ,
		success : function(data) {
			if (data.status == "ERROR") {
				failure( data );
			} else {
				self.me = data.data;
				success( data.data );
			}
		},
		failure : function(err) {
			failure( { code : 100 , message : err } );
		}
	});
}

MemberSite.prototype.logout = function(success, failure) {
	success = this.guard(success);
	failure = this.guard(failure);
	var self = this;
	this.ajax_post({ 
		url : this.apiUrl( "member_site_action.php" ),
		data: { action : "logout" } ,
		success : function(data) {
			if (data.status == "ERROR") {
				failure( data );
			} else {
				self.me = null;
				success();
			}
		},
		failure : function(err) {
			failure( { code : 100 , message : err } );
		}
	});
}

window.memberSite = new MemberSite();

var settings = window.memberSiteSettings;

if ( !(settings && settings.NoLoadLoginData) ) {
	window.addEventListener("DOMContentLoaded" , function() { 
		memberSite.getMe();
	}, false );
}
function logout() {
}


MemberSite.prototype.ajax_post = function(options) {
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
              options.success( JSON.parse(xhr.responseText) ); 
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
