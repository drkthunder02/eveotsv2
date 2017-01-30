<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function StoreCharacterInfo($characterID) {
    $authentication = PrepareESIAuthentication($characterID);
    $config = new \EVEOTS\Config\Config();
    $esiConfig = $config->GetESIConfig();
    $esi = new \Seat\Eseye\Eseye($authentication);
    
    //Try to get the character info from ESI API
    try {
        $character = $esi->invoke('GET', '/character/{character_id}/', [
            'character_id' => $characterID,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //Try to get the corporation info from ESI API
    try {
       $corporation = $esi->invoke('GET', '/corporation/{corporation_id}/', [
           'corporation_id' => $character->corporation_id,
       ]); 
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    //Try to get the alliance info from ESI API
    try {
        $alliance = $esi->invoke('GET', '/alliance/{alliance_id}/', [
            'alliance_id' => $corporation->alliance_id,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    
    //Open the database connection
    $db = DBOpen();
    //Insert the character information into the table
    $db->replace('Characters', array(
        'Character' => $character->name,
        'CharacterID' => $characterID,
        'CorporationID' => $character->corporation_id,
        'Corporation' => $corporation->corporation_name,
    ));
    if($character->corporation_id) {
        //Insert the corporation information into the table
        $db->replace('Corporations', array(
            'CorporationID' => $corporation->corporation_id,
            'Corporation' => $corporation->corporation_name,
            'AllianceID' => $corporation->alliance_id,
            'MemberCount' => $corporation->member_count,
        ));    
    }
    
    
    if($corporation->alliance_id) {
        //Insert the alliance information into the table
        $db->replace('Alliances', array(
            'AllianceID' => $corporation->alliance_id,
            'Alliance' => $alliance->alliance_name,
            'Ticker' => $alliance->ticker,
        )); 
    }
    
    //Close the database connection
    DBClose($db);
}