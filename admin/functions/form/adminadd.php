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
$esi = new EVEOTS\ESI\ESI();

//Encrypt the unique session id in the form of a key for the form
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

//Check to make sure the user is logged in
$user = CheckLogin();
if($user == "") {
    PrintHTMLHeader();
    PrintAdminNavBar($user);
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    printf("</body></html>");
    die();
}
//Check the security level of the user to see if they are allowed access.
$security = CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
if($security['SecurityLevel'] != 1) {
    PrintHTMLHeader();
    PrintAdminNavBar($user);
    printf("div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not authorized to access this area.<br>");
    printf("</div></div>");
    printf("</body></html>");
    die();
}

if(isset($_POST['key'])) {
    $key = filter_input(INPUT_POST, 'key');
} else {
    $key = "";
}

if($unique != $key) {
    printf("Error!");
    die();
}

if(isset($_POST['username'])) {
    $username = filter_input(INPUT_POST, 'username');
} else {
    $username = "";
}

if(isset($_POST['character'])) {
    $character = filter_input(INPUT_POST, 'character');
} else {
    $character = "";
}

if(isset($_POST['password'])) {
    $password = filter_input(INPUT_POST, 'password');
} else {
    $password = "";
}

if(isset($_POST['security'])) {
    $security = filter_input(INPUT_POST, 'security');
} else {
    $security = "";
}

//Check if the user already has an admin account
$verified = $db->fetchRow('SELECT * FROM Admins WHERE username= :user', array('user' => $username));
if($verified != null) {
    $location = 'http://' . $_SERVER['HTTP_HOST'];
    $location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=AdminAddFailDuplicate';
    header("Location: $location");
}

$Character = $esi->SearchESIInfo($character, 'character');
$characterId = $Character['character'][0];
$character = $esi->GetESIInfo($characterId, 'Character');
$corporation = $esi->GetESIInfo($character['corporation_id'], 'Corporation');

$hashPass = md5($password);
$db->insert('Admins', array(
    'username' => $username,
    'password' => $hashPass,
    'characterID' => $characterId,
    'corporationID' => $character['corporation_id'],
    'allianceID' => $corporation['alliance_id'],
    'securityLevel' => $security
));

$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['username'] . " added " . $username . "'s administrator account.";
AddLogEntry($db, $timestamp, $entry);
        
$location = 'http://' . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=AdminAddSuccess';
header("Location: $location");

?>
