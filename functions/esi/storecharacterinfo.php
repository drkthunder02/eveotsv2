<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */


function StoreCharacterInfo($characterID) {
    //Get the configuration from the config class
    $config = new \EVEOTS\Config\Config();
    $configVar = $config->GetESIConfig();
    //Delcare the esi class to be able to get data without rewriting the functions
    $esi = new \EVEOTS\ESI\ESI($configVar['useragent'], $configVar['clientid'], $configVar['secretkey']);    
    //Open the database connection
    $db = DBOpen();
    //Declare our variables before ESI attempts to write to them
    $character = NULL;
    $corporation = NULL;
    $alliance = NULL;
    
    //Try to get the character info from ESI API
    $character = $esi->GetCharacterInfo($characterID);
    //try to get the corporation info from ESI API
    $corporation = $esi->GetCorporationInfo($character['corporation_id']);
    //try to get the alliance info from ESI API
    $alliance = $esi->GetAllianceInfo($corporation['alliance_id']);
    
    //Try to store the information received from ESI API
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
    //Try to store the information received from ESI API
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
    //Try to store the information received from ESI API
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