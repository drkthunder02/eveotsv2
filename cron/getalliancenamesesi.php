<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Only run once a day
//--------------- Run once a day ----------------
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$db = DBOpen();

// create a log channel
$log = new Logger('ESI-Alliance');
$log->pushHandler(new StreamHandler('esi-alliance.log', Logger::WARNING));

//Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
$conf = new EVEOTS\Config\Config();
$config = $conf->GetESIConfig();

$authentication = new \Seat\Eseye\Containers\EsiAuthentication([
    'client_id' => $config['clientid'],
    'secret' => $config['secretkey']
]);
$esi = new \Seat\Eseye\Eseye($authentication);

try {
    $alliances = $esi->invoke('get', '/alliances/names/');
} catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
    // The HTTP Response code and message can be retreived
    // from the exception...
    print $e->getCode() . PHP_EOL;
    print $e->getMessage() . PHP_EOL;
}

foreach($alliances as $alliance) {
    $found = $db->fetchColumn('SELECT AllianceID FROM Alliances WHERE AllianceID= :id', array('id' => $alliance['alliance_id']));
    if($found == false) {
        $db->insert('Alliances', array('AllianceID' => $alliance['alliance_id'], 'Alliance' => $alliance['alliance_name']));
        $log->info("Added AllianceID: " . $alliance['alliance_id'] . "of name: " . $alliance['alliance_name'] . "to the database Alliances table.");
    }
}

//For each Alliance from the database associate a alliance with them
$alliancesDB = $db->fetchRow('SELECT * FROM Alliances');
foreach($alliancesDB as $corpDB) {
    try {
        $alliance = $esi->invoke('get', '/alliances/{alliance_id}/', [
            'alliance_id' => $corp['AllianceID'],
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //If the alliance ID is different then update the Alliances table
    if($alliance['alliance_id'] != $corpDB['AllianceID']) {
        $db->update('Alliances', array('AllianceID' => $corpDB['AllianceID'], array('AllianceID' => $alliance['alliance_id'])));
        $log->info("AllianceID: " . $corpDB['AllianceID'] . "'s alliance ID has been updated.");
    }
        
}

DBClose($db);