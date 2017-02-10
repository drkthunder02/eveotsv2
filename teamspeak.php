<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/functions/registry.php';

//Declaration of variables
$foundName = false;     //For if we have found the name of the client on the server

//Get our POST values from the previous form
if(isset($_POST['characterID'])) {
    $characterID = filter_input('POST', 'characterID');
} else {
    $characterID = 0;
}
if(isset($_POST['blue'])) {
    $blue = filter_input('POST', 'blue');
} else {
    $blue = false;
}
if(isset($_POST['tsname'])) {
    $tsname = filter_input('POST', 'tsname');
} else {
    $tsname = '';
}

//Open the database connection
$db = DBOpen();
//Check to make sure this is a blue and the name has some input
if($blue == false || $tsname == '') {
    printf("<div class=\"container\">
                <div class=\"jumbotron\">
                    <h2>Error 001:  Please try again.</h2>
                </div>
            </div>");
    die();
}

$config = new EVEOTS\Config\Config();
$bluegroup = $config->GetBlueGroup();

//Try to connect to the teamspeak server
try {
    $ts3_VirtualServer = TeamSpeak3::factory($config->GetTSServerQuery());
} catch (TeamSpeak3_Exception $e) {
    die("An error occured: ".$e->getMessage()." [B".__LINE__."]");
}
//Get the client list from the teamspeak server so we can update the database and add user to the correct group
try {
    $clientList = $ts3_VirtualServer->clientList();
} catch (TeamSpeak3_Exception $e) {
    die("An error occured: ".$e->getMessage()." [B".__LINE__."]");
}

//Find the user within the client List
foreach($clientList as $client) {
    if ($client['client_type']) continue;
    //If we have found the name attempt to set permissions
    if($client['client_nickname'] == $tsname) {
        $tsDatabaseID = $client['client_database_id'];
        $tsUniqueID = $client['client_unique_id'];
        $db->update('Users', array('CharacterID' => $characterID), array('TSDatabaseID' => $tsDatabaseID, 'TSUniqueID' => $tsUniqueID));
        $foundName = true;
        break;
    }
}
//Set the permissions for the teamspeak3 client
if($foundName == true) {
    try {
        $ts3_VirtualServer->serverGroupClientAdd($bluegroup, $tsDatabaseID);
    } catch (TeamSpeak3_Exception $e) {
        die("An error occured: ".$e->getMessage()." [B".__LINE__."]");
    }
}






//Print the header for the page
PrintHTMLHeader();
printf("<div class=\"container\">");
printf("<div class=\"jumbotron\">");
printf("<h2>You should now have the correct permissions on the Teamspeak3 server.</h2>");
printf("</div>");
printf("</div>");

//Close the database connection
DBClose($db);

?>

