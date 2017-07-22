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
$urls = array();
$data = array();
$results = array();

//Open the database connection
$db = DBOpen();
//Get the next corporation id to check
$nextCorpRow = $db->fetchColumn('SELECT NextCorporationIdCheck FROM ESICalls');
$maxCorpRow = $db->fetchColumn('SELECT COUNT(id) FROM Corporations');

$Corporations = $db->fetchRowMany('SELECT * FROM Corporations WHERE id>= :next', array('next' => $nextCorpRow));
//Calculate the pages
$pages = ceil(sizeof($Corporations) / $maxEntities);

for($i = 0; $i < sizeof($Corporations); $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/corporations/' . $Corporations[$j]['CorporationID'] . '/?datasource=tranquility';
}

//Send 20 at a time to the server to be processed
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
    
    //Insert the data into the database
    for($j = 0; $j < $maxEntities; $j++) {
        $index = ($i * $maxEntities) + $j;
        if($index == $maxAllianceId) {
            break;
        }
        //Update the corporation infomration
        $db->update('Corporations', array('CorporationID' => $Corporations[$index]['CorporationID']), array(
            'AllianceID' => $results[$j]['alliance_id'],
            'Corporation' => $results[$j]['corporation_name'],
            'MemberCount' => $results[$j]['member_count'],
            'Ticker' => $results[$j]['ticker']
        ));
        //Insert the log entry
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:is'),
            'Type' => 'CorporationCheck',
            'Call' => 'corporationcheck.php',
            'Entry' => 'Updated Corporation of ID ' . $Corporations[$index]['CorporationID'] . '.'
        ));
        //Update the last row modified for ESI
        if($nextCorpRow == $maxCharacterRow) {
            $db->replace('ESICallsCorporation', array('NextCorporationIdCheck' => 1));
        } else {
            $nextCorpRow++;
            $db->replace('ESICallsCorporation', array('NextCorporationIdCheck' => $nextCorpRow));
        }
    }
}

//Close the database connection
DBClose($db);

?>
