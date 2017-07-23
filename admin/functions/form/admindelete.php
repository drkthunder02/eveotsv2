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

//encrypt the unique session id in the form of a key for the form
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

if(isset($_POST['admin'])) {
    $username = filter_input(INPUT_POST, 'admin');
} else {
    $username = "";
}

if($username == "") {
    printf("Error!<br>");
    printf("Username to be deleted not provided.");
    die();
}

$db->delete('Admins', array('username' => $username));

$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['username'] . " deleted " . $username . "'s administrator account.";
AddLogEntry($db, $timestamp, $entry);
        
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=AdminDeleteSuccess';
header("Location: $location");

?>
