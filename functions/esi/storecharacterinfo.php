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
        $charFound = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $characterID));
        if($charFound == false) {
            $db->insert('Characters', array(
                'Character' => $character->name,
                'CharacterID' => $hcaracterID,
                'CorporationID' => $character->corporation_id
            ));
        } else {
            $db->delete('Characters', array('CharacterID' => $hcaracterID));
            $db->insert('Characters', array(
                'Character' => $character->name,
                'CharacterID' => $hcaracterID,
                'CorporationID' => $character->corporation_id
            ));
        }
    }
    
    if($corporation != NULL && $character != NULL) {
        //Insert the corporation information into the table
        $corpFound = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $character->corporation_id));
        if($corpFound == false) {
            $db->insert('Corporations', array(
                'AllianceID' => $corporation->alliance_id,
                'Corporation' => $corporation->corporation_name,
                'CorporationID' => $character->corporation_id,
                'MemberCount' => $corporation->member_count
            ));
        } else {
            $db->delete('Corporations', array('CorporationID' => $hcaracter->corporation_id));
            $db->insert('Corporations', array(
                'AllianceID' => $corporation->alliance_id,
                'Corporation' => $corporation->corporation_name,
                'CorporationID' => $character->corporation_id,
                'MemberCount' => $corporation->member_count,
                'Ticker' => $corporation->ticker
            ));
        } 
    }
    
    if($alliance != NULL && $corporation != NULL) {
        //Insert the alliance information into the table
        $allyFound = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $corporation->alliance_id));
        if($allyFound == false) {
            $db->insert('Alliances', array(
                'Alliance' => $alliance->alliance_name,
                'AllianceID' => $corporation->alliance_id,
                'Ticker' => $alliance->ticker
            ));
        } else {
            $db->delete('Alliances', array('AllianceID' => $corporation->alliance_id));
            $db->insert('Alliances', array(
                'Alliance' => $alliance->alliance_name,
                'AllianceID' => $corporation->alliance_id,
                'Ticker' => $alliance->ticker
            ));
        }
    }    
}