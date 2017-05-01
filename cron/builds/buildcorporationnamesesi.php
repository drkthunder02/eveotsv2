<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

//Record the start time
$start = time();

//Get the debug scope from the configuration file
$config = new \EVEOTS\Config\Config();
$esi = $config->GetESIConfig();
$useragent = $esi['useragent'];
$DEBUG = $config->GetDebugMode();
$maxEntities = 20.0;
$data = array();
$urls = array();
$results = array();

//Open a database connection
$db = DBOpen();

//Get the last corporation id worked on.
$nextCorpName = $db->fetchColumn('SELECT NextCorporationNameBuild FROM ESICallsCorporation WHERE id= :id', array('id' => 1));
$maxCorpName = $db->fetchColumn('SELECT COUNT(id) FROM Corporations');

$pages = ceil($maxCorpId / $maxEntities);

//Build a set of corporations to check
$Corporations = $db->fetchRowMany('SELECT * FROM Corporations');

//Build all of the urls and hold them in memory
for($i = 0; $i < $maxCorpName; $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/corporations/' . $Corporations[$i]['CorporationID'] . '/?datasource=tranquility';
}

//Send 20 at a time to the server to be processed
for($i = 0; $i < $pages; $i++) {
    for($j = 0; $j < $maxEntities; $j++) {
        //Calculate the index currently building
        $urlIndex = ($i * $maxEntities) + $j;
        if($urlIndex == $maxCorpId) {
            break;
        }
        //Add the url index to the data to be send to function
        $data[$j] = $urls[$urlIndex];
    }
    
    //Make the multiple cURL call
    $results = MultipleCURLRequest($data, $useragent);
    
    //Insert the data into the database
    for($j = 0; $j < $maxEntities; $j++) {
        $allianceIndex = ($i * $maxEntities) + $j;
        if($allianceIndex == $maxAllianceId) {
            break;
        }
        $db->update('Corporations', array('CorporationID' => $Corporations[$row]['CorporationID']), array(
            'Corporation' => $results[$j]['corporation_name'],
            'MemberCount' => $results[$j]['member_count'],
            'Ticker' => $results[$j]['ticker']
        ));
        
        //Update that last row worked on
        $row = ($i * $maxEntities) + $j;
        $nextRow = $row + 1;
        if($row == $maxCorpName) {
            $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationNameBuild' => 1));
        } else {
            $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationNameBuild' => $nextRow));
        }

        //Insert a new log entry into the database
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:i'),
            'Type' => 'BuildCorporation',
            'Call' => 'buildcorporationnamesesi.php',
            'Entry' => 'Corporation of name ' . $results[$j]['corporation_name'] . ' updated in the database.'
        ));
    }
    //Display our debug information if debug is set
    if($DEBUG === true) {
        $now = time();
        $seconds = $now - $start;
        printf("Time Take: " . $seconds . " seconds.\n");
    } 
}

//Close the database connection
DBClose($db);

?>
