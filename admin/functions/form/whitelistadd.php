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

//Encryt the unique session id in the form of a key to verify the form
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);


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

//Search CCP's ESI Database for the ID of the entity.
//We are going to assume we don't have this in our own database
$data = $esi->SearchESIInfo($entity, $type);
$id = $data[$type][0];

if($type == 'alliance') {
    $entityType = 3;
} else if ($type == 'corporation') {
    $entityType = 2;
} else if ($type == 'character') {
    $entityType = 1;
}

//Add the entity into the Blue table in the database
//1 - Character
//2 - Corporation
//3 - Alliance
$db->replace('Blues', array(
    'EntityID' => $id,
    'EntityType' => $entityType
));

$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['EVEOTSusername'] . " added " . $entity . " to the server white list.";
AddLogEntry($db, $timestamp, $entry);

//Close the database connection
DBClose($db);

//Redirect back to the admin panel
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListSuccess';
header("Location: $location");

?>
