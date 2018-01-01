<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

$start = time();
$configClass = new \EVEOTS\Config\Config();
$esiClass = new \EVEOTS\ESI\ESI();
$esiConfig = $esiClass->GetESIConfig();
$useragent = $esiConfig['useragent'];
$DEBUG = $configClass->GetDebugMode();
$maxEntities = $esiClass->GetMaxESICalls();
$urls = array();
$data = array();
$results = array();

//Open the database connection
$db = DBOpen();
//Get the next alliance id to check
$nextAllianceRow = $db->fetchColumn('SELECT NextAllianceIdCheck FROM ESICallsAlliance');
$maxAllianceRow = $db->fetchColumn('SELECT COUNT(id) FROM Alliances');

$Alliances = $db->fetchRowMany('SELECT * FROM Alliances WHERE id>= :next', array('next' => $nextAllianceRow));
//Calculate the pages we need to call
$pages = ceil(sizeof($Alliances) / $maxEntities);

//Build the urls to be sent to the api server
for($i = 0; $i < sizeof($Alliances); $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/alliances/' . $Alliances[$i]['AllianceID'] . '/?datasource=tranquility';  
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
        if($index == $maxAllianceRow) {
            break;
        }
        if(!isset($results[$j]['error'])) {
            //Update the alliance information
            $db->update('Alliances', array('AllianceID' => $Alliances[$index]['AllianceID']), array(
                'Alliance' => $results[$j]['name'],
                'Ticker' => $results[$j]['ticker']
            ));
            //Add an entry to the ESI Logs
            $db->insert('ESILogs', array(
                'Time' => gmdate('d.m.Y H:is'),
                'Type' => 'AllianceCheck',
                'Call' => 'alliancecheck.php',
                'Entry' => 'Updated Alliance of ID ' . $Alliances[$index]['AllianceID'] . '.'
            ));
        } else {
            $db->insert('ESILogs', array(
                'Time' => gmdate('d.m.Y H:i:s'),
                'Type' => 'AllianceCheck',
                'Call' => 'alliancecheck.php',
                'Entry' => 'Error pulling ESI data from server.'
            ));
        }
        
        //Update the last row modified for ESI
        if($nextAllianceRow == $maxAllianceRow) {
            $db->replace('ESICallsAlliance', array('NextAllianceIdCheck' => 1));
        } else {
            $nextAllianceRow++;
            $db->replace('ESICallsAlliance', array('NextAllianceIdCheck' => $nextAllianceRow));
        }
    }
}

//Close the database connection after the script is completed
DBClose($db);

?>
