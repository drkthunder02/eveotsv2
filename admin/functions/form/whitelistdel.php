<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//PHP Debug Mode
error_reporting(E_ALL | E_STRICT);

require_once __DIR__.'/../../functions/registry.php';

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

if(isset($_POST['type'])) {
    $type = filter_input(INPUT_POST, 'type');
} else {
    $type = "";
}

if(iiset($_POST['entity'])) {
    $entity = filter_input(INPUT_POST, 'entity', FILTER_SANITIZE_SPECIAL_CHARS);
} else {
    $entity = "";
}

if($entity == "") {
    $location = ServerProtocol() . $_SERVER['HTTP_HOST'];
    $location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListFail';
    header("Location: $location");
    exit;
}

//First search for the entity in our own database
if($type == 'character') {
    $found = $db->fetchRow('SELECT * FROM Characters WHERE Character= :en', array('en' => $entity));
} else if ($type == 'corporation') {
    $found = $db->fetchRow('SELECT * FROM Corporations WHERE Corporation= :en', array('en' => $entity));
} else if ($type == 'alliance') {
    $found = $db->fetchRow('SELECT * FROM Alliances WHERE Alliance= :en', array('en' => $entity));
}

if($found == false || $found == null) {
    $location = ServerProtocol() . $_SERVER['HTTP_HOST'];
    $location = $location. dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListFail';
    header("Location: $location");
    exit;
}

//If the entity was found delete them from the Blue list
if($type == 'character') {
    $db->delete('Blues', array('EntityID' => $found['CharacterID']));
} else if ($type == 'corporation') {
    $db->delete('Blues', array('EntityID' => $found['CorporationID']));
} else if($type == 'alliance') {
    $db->delete('Blues', array('EntityID' => $found['AllianceID']));
}

$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['EVEOTSusername'] . " deleted " . $entity . " from the server's white list.";
AddLogEntry($db, $timestamp, $entry);
DBClose($db);

//Redirect back to the admin panel
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListSuccess';
header("Location: $location");

?>
