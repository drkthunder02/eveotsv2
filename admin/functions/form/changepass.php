<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../../functions/registry.php';

$db = DBOpen();

$session = new Custom\Sessions\session();
$config = new EVEOTS\Config\Config();

//Create the md5 hash to see if it matches the value passed through the form
$uniqueCheck = $_SESSION['key'] . $config->GetSalt();
$uniqueCheck = md5($uniqueCheck);
//Get the unique key passed from the form
if(isset($_POST['key'])) {
    $unique = filter_input(INPUT_POST, 'key');
} else {
    $unique = "";
}

if($unique != $uniqueCheck) {
    printf("Error!");
    die();
}

if(isset($_POST['username'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
} else {
    $username = "";
}

if(isset($_POST['newPass'])) {
    $newPass = filter_input(INPUT_POST, 'newPass');
} else {
    $newPass = "";
}
if(isset($_POST['passConf'])) {
    $passConf = filter_input(INPUT_POST, 'passConf');
} else {
    $passConf = "";
}

PrintAdminHTMLHeader();
PrintAdminNavBar($db, $_SESSION['EVEOTSusername']);

//Check the passwords
if($newPass == "" || $passConf == "") {
    printf("<div class=\"container\">");
    printf("Error: Please fill in both password fields.<br>");
    printf("Please return to the previous page.<br>");
    printf("</div>");
} else if($newPass != $passConf) {
    printf("<div class=\"container\">");
    printf("Passwords didn't matpch.<br>");
    printf("Please return to the previous page and try again.<br>");
    printf("</div>");
} else {
    $newPass = md5($newPass);
    $db->update('Admins', array('username' => $username), array('password' => $newPass));
    $timestamp = gmdate('d.m.Y H:i');
    $entry = $_SESSION['EVEOTSusername'] . " changed " . $username . "'s password.";
    AddLogEntry($db, $timestamp, $entry);
}

DBClose($db);

$location = 'http://' . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../admin_panel.php?msg=AdminPasswordSuccess';
header("Location: $location");