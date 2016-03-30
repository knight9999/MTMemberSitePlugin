<?php 

// PHP Lib

function isLogined() {
	$me = me();
	if ($me) {
		return true;
	}
	return false;
}

function me($key = null) {
	session_start();
	if (isset($_SESSION['me'])) {
		$me = $_SESSION["me"];
	} else {
		$me = null;
	}
	session_write_close();
	if ($key != null) {
		return $me[$key];
	}
	return $me;
}


?>