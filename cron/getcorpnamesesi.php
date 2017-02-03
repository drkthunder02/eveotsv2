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
$log = new Logger('ESI-Corporation');
$log->pushHandler(new StreamHandler('esi-corporation.log', Logger::WARNING));

//Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
$conf = new EVEOTS\Config\Config();
$config = $conf->GetESIConfig();

$authentication = new \Seat\Eseye\Containers\EsiAuthentication([
    'client_id' => $config['clientid'],
    'secret' => $config['secretkey']
]);
$esi = new \Seat\Eseye\Eseye($authentication);

try {
    $corporations = $esi->invoke('get', '/corporations/names/');
} catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
    // The HTTP Response code and message can be retreived
    // from the exception...
    print $e->getCode() . PHP_EOL;
    print $e->getMessage() . PHP_EOL;
}

foreach($corporations as $corporation) {
    $found = $db->fetchColumn('SELECT CorporationID FROM Corporations WHERE CorporationID= :id', array('id' => $corporation['corporation_id']));
    if($found == false) {
        $db->insert('Corporations', array('CorporationID' => $corporation['corporation_id'], 'Corporation' => $corporation['corporation_name']));
        $log->info("Added CorporationID: " . $corporation['corporation_id'] . "of name: " . $corporation['corporation_name'] . "to the database Corporations table.");
    }
}

//For each Corporation from the database associate a corporation with them
$corporationsDB = $db->fetchRow('SELECT * FROM Corporations');
foreach($corporationsDB as $corpDB) {
    try {
        $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
            'corporation_id' => $corp['CorporationID'],
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //If the corporation ID is different then update the Corporations table
    if($corporation['corporation_id'] != $corpDB['CorporationID']) {
        $db->update('Corporations', array('CorporationID' => $corpDB['CorporationID'], array('CorporationID' => $corporation['corporation_id'])));
        $log->info("CorporationID: " . $corpDB['CorporationID'] . "'s corporation ID has been updated.");
    }
        
}

DBClose($db);