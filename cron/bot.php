<?php
/*
========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ==========
*/
// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
// Required files
require_once __DIR__.'/../functions/registry.php';

//Activate the configuration class
$config = new \EVEOTS\Config\Config();
//Open the database connection
$db = DBOpen();

//Declare variables
$kicked = 0;
$users = 0;

//Attempt to connect to Teamspeak3
try {
    $ts3_VirtualServer = TeamSpeak3::factory($config->GetTSServerQuery());
} catch (TeamSpeak3_Exception $e) {
    die("An error occured: ".$e->getMessage()." [B".__LINE__."]");
}
//Get the client list from the Teamspeak3 server
try {
    $clientList = $ts3_VirtualServer->clientList();
} catch (TeamSpeak3_Exception $e) {
    die("An error occured: " . $e->getMessage() . " [B" .__LINE__ . "]");
}

//For each person in the Teamspeak3 client, check versus the database and perform the appropriate
//action and log such actions
foreach($clientList as $client) {
    //If we have a valid client type from the Teamspeak3 server
    if($client['client_type']) continue;
    //Get the client database id from the Teamspeak3 server
    $client_database_id = $client['client_database_id'];
    //Check if the user is already registered
    $reg = $db->fetchRow('SELECT * FROM Users WHERE TSDatabaseID= :id', array('id' => $client_database_id));
    $rowCount = $db->getRowCount();
    if($rowCount == 0) {
        //Send a message to the user not registered, to register on teamspeak
        //$ts3_VirtualServer->clientGetByName($client['client_nickname'])->poke("Please register on the teamspeak server.");
        $log = Format("Skipping user, not registered (" . $client['client_nickname'] . ")\n",
               "Skipping user, not registered (" . $client['client_nickname'] . "<br>");
        $date = gmdate('m.d.Y H:i');
        printf($date . ": " . $log);
    } else {
        $tsDatabaseID = $reg['TSDatabaseID'];
        $tsUniqueID = $reg['TSUniqueID'];
        $tsName = $reg['TSName'];
        $log = Format("Processing: " . $tsName . "\n", "Processing: " . $tsName . "<br>");
        $date = gmdate('m.d.Y H:i');
        printf($date . ": " . $log);
        $users++;
        if($client['client_nickname'] != $tsName && $client['client_nickname'] != $tsName . "1") {
            try {
                $tsUnqiueID = str_replace("'", "", $tsUniqueID);
                $ts3_VirtualServer->clientGetByUid($tsUniqueID)->Kick(TeamSpeak3::KICK_SERVER, "SecurityBot: Your nickname should be exactly ".$tsName);
                $log = Format(">>> Kicked user ".$tsName.", their name was ".$client['client_nickname']."\n","Kicked user ".$tsName.", their name was ".$client['client_nickname']."<br />");
                $date = gmdate('m.d.Y H:i');
                printf($date . ": " . $log);
                $kicked++;
            } catch (TeamSpeak3_Exception $e) {
                try {
                    $ts3_VirtualServer->clientGetByName($client['client_nickname'])->Kick(Teamspeak3::KICK_SERVER, "SecurityBot: Your nickname should be exactly " . $tsName);
                    $log = Format(">>> Kicked user ".$tsName.", their name was ".$client['client_nickname']."\n","Kicked user ".$tsName.", their name was ".$client['client_nickname']."<br />");
                    $date = gmdate('m.d.Y H:i');
                    printf($date . ": " . $log);
                    $kicked++;
                } catch (TeamSpeak_Exception $ex) {
                    $log = Format("Debug: User ".$tsName." could not be kicked. (Error: ".$e->getMessage().")\n", "Debug: User ".$tsName." could not be kicked. Probably wasn't connected in the first place. (Error: ".$e->getMessage().")<br />");
                    $date = gmdate('m.d.Y H:i');
                    printf($date . ": " . $log);
                }
                
                $log = Format("Debug: User ".$tsName." could not be kicked. (Error: ".$e->getMessage().")\n", "Debug: User ".$tsName." could not be kicked. Probably wasn't connected in the first place. (Error: ".$e->getMessage().")<br />");
                $date = gmdate('m.d.Y H:i');
                printf($date . ": " . $log);
            }
        }
    }
}

//Close the database connection
DBClose($db);

//Log the details of the bot operation
$date = gmdate('m.d.Y H:i');
printf($date . ": " . $users . " users checked, " . $kicked . " users kicked.");

?>