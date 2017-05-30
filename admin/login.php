<?php
/*
========== * EVE ONLINE TEAMSPEAK BY MJ MAVERICK * ==========
*/
// PHP debug mode
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
// Required files

require_once __DIR__.'/functions/registry.php';
require_once __DIR__.'/../functions/registry.php';

$config = new \EVEOTS\Config\Config();
$session = new \Custom\Sessions\session();
ob_start();

$db = DBOpen();

//Get the username and password from the post
$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, "password");
$password = md5($password);

$admins = $db->fetchRowMany('SELECT * FROM Admins WHERE username= :user AND password= :pass', array('user' => $username, 'pass' => $password));
$count = $db->getRowCount();

if($count == 1) {
    $user = $db->fetchRow('SELECT * FROM Admins WHERE username= :user', array('user' => $username));
    $_SESSION["EVEOTSusername"] = $username;
    $_SESSION["EVEOTSid"] = $user['id'];
    $_SESSION["key"] = uniqid();
    header("location:admin_panel.php");
} else {
    echo "Wrong Username or Password<br />";
    if ($config->GetDebugMode() == true) {
            echo "Debug: Password: ".$password."<br />";
    }
    echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
}

ob_end_flush();

?>
