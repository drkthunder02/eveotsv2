<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

$start = time();
$configClass = new \EVEOTS\Config\Config();
$config = $configClass->GetESIConfig();
$useragent = $config['useragent'];
$DEBUG = $configClass->GetDebugMode();
$maxEntities = $configClass->GetMaxESICalls();

//Open the database connection
$db = DBOpen();

//Get the next character id to check
$nextCharacterRow = $db->fetchColumn('SELECT NextCharacterIdCheck FROM ESICallsCharacter');
$maxCharacterRow = $db->fetchColumn('SELECT COUNT(id) FROM Characters');

//Store in memory the characters to be checked
//This will return all of the unchecked characters from the last time we stopped
$Characters = $db->fetchRowMany('SELECT * FROM Characters WHERE id>= :next', array('next' => $nextCharacterRow));
//Calculate how many pages we need to call
$pages = ceil(sizeof($Characters) / $maxEntities);

//Build the URLs to be checked this run time
for($i = 0; $i < sizeof($Characters); $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/characters/' . $Characters[$i]['CharacterID'] . '/?datasource=tranquility';
}

//Send our calls each page at a time to the CCP ESI server
for($i = 0; $i < $pages; $i++) {
    for($j = 0; $j < $maxEntities; $j++) {
        //Calculate the index being curently built
        $urlIndex = ($i * $maxEntities) + $j;
        if($urlIndex == $maxAllianceRow) {
            break;
        }
        //Add the url index to the data to be sent
        $data[$j] = $urls[$urlIndex];
    }
    //Make the multiple cURL calls
    $results = MultipleCURLRequest($data, $useragent);
    
    //Update the database with the refreshed data from CCP ESI server
    for($j = 0; $j < $maxEntities; $j++) {
        $index = ($i * $maxEntities) + $j;
        if($index == $maxCharacterRow) {
            break;
        }
        //Update the character's data in the database
        $db->update('Characters', array('CharacterID' => $Characters[$index]['CharacterID']), array(
            'Character' => $results[$j]['name'],
            'CorporationID' => $results[$j]['corporation_id']
        ));
        //Update the last row modified for ESI
        if($nextCharacterRow == $maxCharacterRow) {
            $db->replace('ESICallsCharacter', array('NextCharacterIdCheck' => 1));
        } else {
            $nextCharacterRow++;
            $db->replace('ESICallsCharacter', array('NextCharacterIdCheck' => $nextCharacterRow));
        }
        //Insert our log into the ESI Logs
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:i:s'),
            'Type' => 'CharacterCheck',
            'Call' => 'charactercheck.php',
            'Entry' => 'Updated Character of ID ' . $Characters[$index]['CharacterID'] . '.'
        ));
    }
}

//Close the database connection after the script is completed
DBClose($db);

?>
