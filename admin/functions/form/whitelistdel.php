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
    printf("Error!  Invalid verification key.<br>");
    die();
}

if(isset($_POST['entity'])) {
    $Entity = filter_input(INPUT_POST, 'entity');
} else {
    $Entity = "";
}

if($Entity == "") {
    $location = ServerProtocol() . $_SERVER['HTTP_HOST'];
    $location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListFail';
    header("Location: $location");
    exit;
}

$data = json_decode($Entity, true);
$entityID = $data['EntityID'];
$type = $data['EntityType'];

$db->delete('Blues', array('EntityID' => $entityID, 'EntityType' => $type));

$timestamp = gmdate('d.m.y H:i');
$entry = $_SESSION['EVEOTSusername'] . " deleted " . $entity . " from the server's white list.";
AddLogEntry($db, $timestamp, $entry);
DBClose($db);

//Redirect back to the admin panel
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListSuccess';
header("Location: $location");

?>
