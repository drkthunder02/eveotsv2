<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);


require_once __DIR__.'/functions/registry.php';

//Declaration of variables
$foundName = false;     //For if we have found the name of the client on the server

$session = new Custom\Sessions\session();
$config = new EVEOTS\Config\Config();

//Encrypt the unique session id in the form of a key for the form
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintHTMLHeader();
printf("<body>");

//Check our unique key to validate the form
if(isset($_POST['key'])) {
    $key = filter_input(INPUT_POST, 'key');
} else {
    $key = "";
}

if($unique != $key) {
    printf("Error!");
    die();
}

//Get our POST values from the previous form
if(isset($_POST['characterID'])) {
    $characterID = filter_input(INPUT_POST, 'characterID');
} else {
    $characterID = 0;
}
if(isset($_POST['corporationID'])) {
    $corporationID = filter_input(INPUT_POST, 'corporationID');
} else {
    $corporationID = 0;
}
if(isset($_POST['allianceID'])) {
    $allianceID = filter_input(INPUT_POST, 'allianceID');
} else {
    $allianceID = 0;
}
if(isset($_POST['blue'])) {
    $blue = filter_input(INPUT_POST, 'blue');
} else {
    $blue = false;
}
if(isset($_POST['us'])) {
    $us = filter_input(INPUT_POST, 'us');
} else {
    $us = false;
}
if(isset($_POST['tsname'])) {
    $tsname = filter_input(INPUT_POST, 'tsname');
    if(strlen($tsname)>30) {
        $tsname = substr($tsname,0,30);
    }
} else {
    $tsname = '';
}

//Open the database connection
$db = DBOpen();
//Check to make sure this is a blue and the name has some input
if($tsname == '' || ($us == false && $blue == false)) {
    printf("<div class=\"container\">
                <div class=\"jumbotron\">
                    <h2>Error 001:  Please try again.</h2>
                </div>
            </div>");
    die();
}


if($us == true) {
    $usergroup = $config->GetMainGroup();
} else if($blue == true) {
    $usergroup = $config->GetBlueGroup();
}

//Try to connect to the teamspeak server
try {
    $ts3_VirtualServer = TeamSpeak3::factory($config->GetTSServerQuery());
} catch (TeamSpeak3_Exception $e) {
    printf("Couldn't connect to the teamspeak server.");
    die("An error occured: ".$e->getMessage()." [B".__LINE__."]");
}
//Get the client by the nickname they should have on the server
try {
    $tsClient = $ts3_VirtualServer->clientGetByName($tsname);
    $tsDatabaseID = $tsClient->client_database_id;
    $tsUniqueID = $tsClient->client_unique_identifier;
}  catch (TeamSpeak3_Exception $e) {
    die("Error: Could not find you on the server, your nickname should be exactly \"$tsname\" (Error: ".$e->getMessage()." [F".__LINE__."])");
}
//Attempt to add them to the server group
try {
    $ts3_VirtualServer->clientGetByName($tsname)->addServerGroup($usergroup);
} catch (TeamSpeak3_Exception $e) {
    die("Error: Could not find you on the server, your nickname should be exactly '" . $tsname . "'. Either that or you already have permissions. (Error: ".$e->getMessage()." [F".__LINE__."])");
}
//Attempt to store the details of the user in the database
try {
    //Store the info into the database.
    $tsUniqueID = "'" . $tsUniqueID . "'";
    $db->update('Users', array('CharacterID' => $characterID), array(
        'TSUniqueID' => $tsUniqueID,
        'TSDatabaseID' => $tsDatabaseID
    ));
    
} catch (\Simplon\Mysql\MysqlException $e) {
    $tsClient->remServerGroup($usergroup);
    die("Error: Failed to INSERT new member. (Error: ".$e->getMessage()." [F".__LINE__."])");
}


printf("<div class=\"jumbotron\">");
printf("<div class=\"container\">");
printf("<h3>You should now have the correct permissions on the Teamspeak3 server.<br>");
printf("</div>");
printf("</div>");

//Close the database connection
DBClose($db);

?>

