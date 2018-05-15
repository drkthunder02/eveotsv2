<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

$start = time();
$configClass = new \EVEOTS\Config\Config();
$DEBUG = $configClass->GetDebugMode();
$maxEntities = $esiClass->GetMaxESICalls();

$usWhiteList = $config->GetMainAlliance();

//Open the database connection
$db = DBOpen();
//Get the list of users on the server
$Users = $db->fetchRowMany('SELECT * FROM Users');
$Blues = $db->fetchRowMany('SELECT * FROM Blues');
//Check each user against the Blues list and the Us List.
//If it's not us anymore or a blue, remove their data from the Users table.
foreach($Users as $user) {
    //Get the properties of the character
    $char = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :char', array('char' => $user['CharacterID']));
    $corp = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :corp', array('corp' => $char['CorporationID']));
    
    //Check whether the user should be removed or not
    $remove = CheckBlueStatus($Blues, $usWhiteList, $characterId, $corporationId);
    
    if($remove == true) {
        $db->delete('Users', array(
            'CharacterID' => $user['CharacterID']
        ));
        
        $db->insert('Logs', array(
            'time' => gmdate('d.m.Y H:i:s'),
            'entry' => 'User, ' . $user['CharacterId'] . 'deleted from user list by scheduled task.'
        ));
    }
    
    
    
    
}

