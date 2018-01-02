<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function StoreSSOData($CharacterID, $Character, $Corporation, $Alliance) {
    //Open the database connection
    $db = DBOpen();
    
    //Search to see if the character is already found in the database
    $charFound = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $CharacterID));
    if($charFound == false) { //If the character is not found, enter into the database
        $db->insert('Characters', array(
            'CorporationID' => $Character['corporation_id'],
            'Character' => $Character['name'],
            'CharacterID' => $CharacterID
        ));
    } else { //If it is found, update the data
        $db->update('Characters', array('CharacterID' => $CharacterID), array(
            'CorporationID' => $Character['corporation_id'],
            'Character' => $Character['name'],
            'CharacterID' => $CharacterID
        ));
    }
    
    //Search the database to see if the corporation is already in the database
    $corpFound = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $Character['corporation_id']));
    if($corpFound == false) { //If it is not found, insert into the database
        if($alliance != null) {
            $db->insert('Corporations', array(
                'AllianceID' => $Corporation['alliance_id'],
                'Corporation' => $Corporation['name'],
                'CorporationID' => $Character['corporation_id'],
                'MemberCount' => $Corporation['member_count'],
                'Ticker' => $Corporation['ticker']
            ));
        } else {
            $db->insert('Corporations', array(
                'Corporation' => $Corporation['name'],
                'CorporationID' => $Character['corporation_id'],
                'MemberCount' => $Corporation['member_count'],
                'Ticker' => $Corporation['ticker']
            ));
        }
        
    } else { //If it is found, update the data
        if($alliance != null) {
            $db->update('Corporations', array('CorporationID' => $Character['corporation_id']), array(
                'AllianceID' => $Corporation['alliance_id'],
                'Corporation' => $Corporation['name'],
                'CorporationID' => $Character['corporation_id'],
                'MemberCount' => $Corporation['member_count'],
                'Ticker' => $Corporation['ticker']
            ));
        } else {
            $db->update('Corporations', array('CorporationID' => $Character['corporation_id']), array(
                'Corporation' => $Corporation['name'],
                'CorporationID' => $Character['corporation_id'],
                'MemberCount' => $Corporation['member_count'],
                'Ticker' => $Corporation['ticker']
            ));
        }
        
    }
    
    //Search the database to see if the alliance is already in the database
    $allianceFound = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $Corporation['alliance_id']));
    if($allianceFound == false) {
        $db->insert('Alliances', array(
            'Alliance' => $Alliance['name'],
            'AllianceID' => $Corporation['alliance_id'],
            'Ticker' => $Alliance['ticker']
        ));
    } else {
        $db->update('Alliances', array('AllianceID' => $Corporation['alliance_id']), array(
            'Alliance' => $Alliance['name'],
            'AllianceID' => $Corporation['alliance_id'],
            'Ticker' => $Alliance['ticker']
        ));
    }
    
    //Close the database connection
    DBClose($db);
}