<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Include the required files
require_once __DIR__.'/../../functions/registry.php';

//Record the start time of the file
$start = time();

//Get the debug scope from the configuration file
$config = new \EVEOTS\Config\Config();
$esi = new EVEOTS\ESI\ESI();
$useragent = $esi['useragent'];
$DEBUG = $config->GetDebugMode();
$maxEntities = $esi->GetMaxESICalls();
$urls = array();
$data = array();
$results = array();

//Open the database connection
$db = DBOpen();

//Get the last alliance id worked on
$nextAllianceId = $db->fetchColumn('SELECT NextAllianceNameBuild FROM ESICallsAlliance WHERE id= :id', array('id' => 1));
$maxAllianceId = $db->fetchColumN('SELECT COUNT(id) FROM Alliances');

//Get all of the alliances 
$Alliances = $db->fetchRowMany('SELECT * FROM Alliances');
//Calculate the pages
$pages = ceil($maxAllianceId / $maxEntities);

for($i = 0; $i < $maxAllianceId; $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/alliances/' . $Alliances[$i]['AllianceID'] . '/?datasource=tranquility';
}

//Send 20 at a time to the server to be processed
for($i = 0; $i < $pages; $i++) {
    for($j = 0; $j < $maxEntities; $j++) {
        //Calculate the index being curently built
        $urlIndex = ($i * $maxEntities) + $j;
        if($urlIndex == $maxAllianceId) {
            break;
        }
        //Add the url index to the data to be sent
        $data[$j] = $urls[$urlIndex];
    }
    
    //Make the multiple cURL calls
    $results = MultipleCURLRequest($data, $useragent);
    
    //Insert the data into the database
    for($j = 0; $j < $maxEntities; $j++) {
        $allianceIndex = ($i * $maxEntities) + $j;
        if($allianceIndex == $maxAllianceId) {
            break;
        }
        $db->update('Alliances', array('AllianceID' => $Alliances[$allianceIndex]['AllianceID']), array(
            'Alliance' => $results[$j]['name'],
            'Ticker' => $results[$j]['ticker']
        ));
        
        //Update the last row worked on
        $row = ($i * $maxEntities) + $j;
        $nextRow = $row + 1;
        if($row == $maxAllianceId) {
            $db->update('ESICallsAlliance', array('id' => 1), array('NextAllianceNameBuild' => 0));
        } else {
            $db->update('ESICallsAlliance', array('id' => 1), array('NextAllianceNameBuild' => $nextRow));
        }
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:i'),
            'Type' => 'BuildAlliances', 
            'Call' => 'buildalliancenamesesi.php', 
            'Entry' => 'Alliance of name ' . $results[$j]['name'] . ' added to the database.'
        ));
    }
    //Check if we need to display stuff for debugging purposes
    if($DEBUG == true) {
        $now = time();
        $seconds = $now - $start;
        printf("Time Taken: " . $seconds . " seconds.\n");
    }
}

//Close the database connection
DBClose($db);

?>
