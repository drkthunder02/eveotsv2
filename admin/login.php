<?php
/*
========== * EVE ONLINE TEAMSPEAK BY MJ MAVERICK * ==========
*/
// PHP debug mode
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
// Required files
require_once("../config.php");
require_once("../version.php");
// Activate config
$c = new Config;
$v = new Version;
//--------------------------------------------------------------------------------------------------------
ob_start();
// Connect to server and select database.
$conLOGIN = mysql_connect($c->db_host,$c->db_user,$c->db_pass);
	if (!$conLOGIN ) {
		die("Could not connect: " . mysql_error()." [L".__LINE__."]");
	}
$db_selectLOGIN = mysql_select_db($c->db_name, $conLOGIN);
	if (!$db_selectLOGIN ) {
		die("Could not select database: " . mysql_error()." [L".__LINE__."]");
	}

// Define $myusername and $mypassword 
$username = $_POST["username"]; 
$password = $_POST["password"];

// To protect MySQL injection (more detail about MySQL injection)
$username = stripslashes($username);
$password = stripslashes($password);
$username = mysql_real_escape_string($username);
$password = mysql_real_escape_string($password);
$password = md5($password);

$sql = "SELECT * FROM admins WHERE username = \"$username\" AND password = \"$password\";";
$result = mysql_query($sql);

// Mysql_num_row is counting table row
$count = mysql_num_rows($result);
// If result matched $username and $password, table row must be 1 row
if ($count == 1) {
	$con = mysql_connect($c->db_host,$c->db_user,$c->db_pass);
		if (!$con) {
			die("Could not connect: " . mysql_error()." [L".__LINE__."]");
		}
	$db_select = mysql_select_db($c->db_name, $con);
		if (!$db_select) {
			die("Could not select database: " . mysql_error()." [L".__LINE__."]");
		}
	$query = mysql_query("SELECT * FROM admins WHERE username=\"$username\";");
	while ($row = mysql_fetch_array($query)) {
			$id = "$row[id]";
			$username = "$row[username]";
	}
	mysql_close($con);
	// Register $username, $password, id and redirect to file "admin_panel.php"
	session_start();
		$_SESSION["EVEOTSusername"] = $username;
		$_SESSION["EVEOTSpassword"] = $password;
		$_SESSION["EVEOTSid"] = $id;
	session_write_close();
	header("location:admin_panel.php");
} else {
	echo "Wrong Username or Password<br />";
	if ($c-verbose == true) {
		echo "Debug: Password: ".$password."<br />";
	}
	echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
}
ob_end_flush();
?>