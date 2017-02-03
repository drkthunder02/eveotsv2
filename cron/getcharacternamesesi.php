<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//--------------- Run once a day ----------------
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$db = DBOpen();

// create a log channel
$log = new Logger('ESI-Character');
$log->pushHandler(new StreamHandler('esi-character.log', Logger::WARNING));

//Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
$conf = new EVEOTS\Config\Config();
$config = $conf->GetESIConfig();

$authentication = new \Seat\Eseye\Containers\EsiAuthentication([
    'client_id' => $config['clientid'],
    'secret' => $config['secretkey']
]);
$esi = new \Seat\Eseye\Eseye($authentication);

try {
    $characters = $esi->invoke('get', '/characters/names/');
} catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
    // The HTTP Response code and message can be retreived
    // from the exception...
    print $e->getCode() . PHP_EOL;
    print $e->getMessage() . PHP_EOL;
}

foreach($characters as $character) {
    $found = $db->fetchColumn('SELECT CharacterID FROM Characters WHERE CharacterID= :id', array('id' => $character['character_id']));
    if($found == false) {
        $db->insert('Characters', array('CharacterID' => $character['character_id'], 'Character' => $character['character_name']));
        $log->info("Added CharacterID: " . $character['character_id'] . "of name: " . $character['character_name'] . "to the database Characters table.");
    }
}

//For each Character from the database associate a corporation with them
$charactersDB = $db->fetchRow('SELECT * FROM Characters');
foreach($charactersDB as $charDB) {
    try {
        $character = $esi->invoke('get', '/characters/{character_id}/', [
            'character_id' => $char['CharacterID'],
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //If the corporation ID is different then update the Characters table
    if($character['corporation_id'] != $charDB['CorporationID']) {
        $db->update('Characters', array('CharacterID' => $charDB['CharacterID'], array('CorporationID' => $character['corporation_id'])));
        $log->info("CharacterID: " . $charDB['CharacterID'] . "'s corporation ID has been updated.");
    }
        
}

DBClose($db);