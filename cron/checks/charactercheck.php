<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../functions/registry.php';

$start = time();

//Open the database connection
$db = DBOpen();

//Get the next character id to check
$nextCharacterRow = $db->fetchColumn('SELECT NextCharacterIdCheck FROM ESICallsCharacter');
$maxCharacterRow = $db->fetchColumn('SELECT COUNT(id) FROM Characters');

$configClass = new \EVEOTS\Config\Config();
$config = $configClass->GetESIConfig();

$esiCall = new \EVEOTS\ESI\ESI($config['useragent'], $config['clientid'], $config['secretkey']);

for($row = $nextCharacterRow; $row <= $maxCharacterRow; $row++) {
    //Get the character information from the database
    $characterDB = $db->fetchRow('SELECT * FROM Characters WHERE id= :id', array('id' => $row));
    $Character = $esiCall->GetCharacterInfo($characterDB['CharacterID']);
    if($Character == null) {
        die();
    } else {
        if(($characterDB['CorporationID'] != $Character['corporation_id'] || $characterDB['Character'] != $Character['character_name']) && !isset($Character['error'])) {
            $db->update('Characters', array('CharacterID' => $charEsi['character_id']), array(
                'Character' => $Character['character_name'],
                'CorporationID' => $Character['corporation_id']
            ));
        }
        //Update the last row modified for ESI
        $nextRow = $row + 1;
        if($row == $maxCharacterRow) {
            $db->replace('ESICallsCharacter', array('NextCharacterIdCheck' => 1));
        } else {
            $db->replace('ESICallsCharacter', array('NextCharacterIdChec' => $nextRow));
        }
    }
}

//Close the database connection after the script is completed
DBClose($db);

?>
