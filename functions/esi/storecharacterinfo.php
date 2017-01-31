<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */


function StoreCharacterInfo($characterID) {

    //Open the database connection
    $db = DBOpen();
    $authentication = PrepareESIAuthentication($characterID);
    $esi = new \Seat\Eseye\Eseye($authentication);
    //Declare our variables before ESI attempts to write to them
    $character = NULL;
    $corporation = NULL;
    $alliance = NULL;
    
    //Try to get the character info from ESI API
    try {
        $character = $esi->invoke('get', '/characters/{character_id}/', [
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
       $corporation = $esi->invoke('get', '/corporations/{corporation_id}/', [
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
        $alliance = $esi->invoke('get', '/alliances/{alliance_id}/', [
            'alliance_id' => $corporation->alliance_id,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        // The HTTP Response code and message can be retreived
        // from the exception...
        print $e->getCode() . PHP_EOL;
        print $e->getMessage() . PHP_EOL;
    }
    if($character != NULL && $corporation != NULL) {
        //Insert the character information into the table
        $db->replace('Characters', array(
            'Character' => $character->name,
            'CharacterID' => $characterID,
            'CorporationID' => $character->corporation_id,
            'Corporation' => $corporation->corporation_name
        ));
    }
    
    if($corporation != NULL && $character != NULL) {
        //Insert the corporation information into the table
        $db->replace('Corporations', array(
            'AllianceID' => $corporation->alliance_id,
            'Corporation' => $corporation->corporation_name,
            'CorporationID' => $character->corporation_id,
            'MemberCount' => $corporation->member_count
        ));    
    }
    
    
    if($alliance != NULL && $corporation != NULL) {
        //Insert the alliance information into the table
        $db->replace('Alliances', array(
            'AllianceID' => $corporation->alliance_id,
            'Alliance' => $alliance->alliance_name,
            'Ticker' => $alliance->ticker
        )); 
    }
    
    //Close the database connection
    DBClose($db);
}