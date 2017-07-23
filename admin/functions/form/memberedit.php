<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//PHP Debug Mode
error_reporting(E_ALL | E_STRICT);

require_once __DIR__.'/../functions/registry.php';

$db = DBOpen();

$session = new Custom\Sessions\session();
$config = new EVEOTS\Config\Config();

//Encryt the unique session id in the form of a key to verify the form
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintAdminHTMLHeader();
PrintAdminNavBar($_SESSION['EVEOTSusername']);

if(isset($_POST['key'])) {
    $key = filter_input(INPUT_POST, 'key');
} else {
    $key = "";
}

//Check to make sure the form is correct
if($unique != $key) {
    printf("Error!");
    die();
}

if(isset($_POST['delete'])) {
    $delete = filter_input(INPUT_POST, 'delete');
} else {
    $delete = "";
}

//Get the user data to be deleted in order to make a log entry
$userDel = $db->fetchRow('SELECT * FROM Users WHERE id= :id', array('id' => $delete));
$userDelChar = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $userDel['CharacterID']));
//Delete the user from the database
$db->delete('Users', array('id' => $delete));
$msg = 'MemberDeleteSuccess';
//Try to remove the user from teamspeak
try {
    $ts3_VirtualSever = Teamspeak3::factory($config->GetTSServerQuery());
} catch (Teamspeak3_Exception $e) {
    printf("Error: " . $e->getMessage() . " [A" . __LINE__ . "]");
    $timestamp = gmdate('d.m.y H:i');
    $entry = 'Failure to connect to teamspeak3 server';
    AddLogEntry($db, $timestamp, $entry);
}
//Check if the client is online and kick if they are
try {
    $online = $ts3_VirtualServer->clientGetIdsByUid($userDel['TSUniqueID']);
    try {
        $ts3_VirtualServer->clientGetByUid($tsUniqueID)->Kick(TeamSpeak3::KICK_SERVER, "Teamspeak access revoked by ".$_SESSION["EVEOTSusername"].".");
    } catch (Exception $ex) {
        printf("FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]");
        $timestamp = gmdate('d.m.y H:i');
        $entry = "Failed to get " . $userDelChar['Character'] . "'s TSUnique ID.";
        AddLogEntry($db, $timestamp, $entry);
        $msg = 'MemberDeleteTSFail';
    }
} catch (TeamSpeak3_Exception $e) {
    printf("FAILED> (Error: " . $e->getMessage() . ") [A" . __LINE__ . "]");
    $timestamp = gmdate('d.m.y H:i');
    $entry = "Failed to get " . $userDelChar['Character'] . "'s uid for teamspeak.";
    AddLogEntry($db, $timestamp, $entry);
    $msg = 'MemberDeleteTSFail';
}

//Create the log entry
$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['EVEOTSusername'] . " deleted " . $userDelChar['Character'] . "'s teamspeak privileges.";
AddLogEntry($db, $timestamp, $entry);
//Close the database connection
DBClose($db);

//Redirect back to the admin_panel
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=' . $msg;
header("Location: $location");

?>
