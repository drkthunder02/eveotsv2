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

for($row = $nextCharacterRow; $row <= $maxCharacterRow; $row++) {
    //Get the character information from the database
    $characterDB = $db->fetchRow('SELECT * FROM Characters WHERE id= :id', array('id' => $row));
    //Let's build the ESI Call
    $url = 'https://esi.tech.ccp.is/latest/characters/' . $characterDB['CharacterID'] . '/?datasource=tranquility';
    $header = 'Accept: application/json';
    $useragent = 'EVEOTSv2 Auth';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    //If there is a curl error, then error out of the script
    if(curl_error($ch)) {
        die();
    } else {
        $charEsi = json_decode($result, true);
        if(($characterDB['CorporationID'] != $charEsi['corporation_id'] || $characterDB['Character'] != $charEsi['character_name']) && !isset($charEsi['error'])) {
            $db->update('Characters', array('CharacterID' => $charEsi['character_id']), array(
                'Character' => $charEsi['character_name'],
                'CorporationID' => $charEsi['corporation_id']
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
    
    //Close the curl call
    curl_close($ch);
    //Sleep for 1 second
    sleep(1);
}

//Close the database connection after the script is completed
DBClose($db);

?>
