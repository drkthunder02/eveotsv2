<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../../functions/registry.php';

//Record the start time of the file
$start = time();

//Get the debug scope from the configuration file
$config = new \EVEOTS\Config\Config();
$esi = new EVEOTS\ESI\ESI();
$useragent = $esi['useragent'];
$DEBUG = $config->GetDebugMode();
$maxEntities = $esi->GetMaxESICalls();
$data = array();
$urls = array();
$results = array();

//Open the database
$db = DBOpen();

//Get the last alliance id worked on.
$nextAllianceId = $db->fetchColumn('SELECT NextCorporationIdBuild FROM ESICallsCorporation');
$maxAllianceId = $db->fetchColumn('SELECT COUNT(id) FROM Alliances');

$pages = ceil($maxAllianceId / $maxEntities);

//Get all of the alliances from the database so we can populate
//the corporation IDs
$Alliances = $db->fetchRowMany('SELECT * FROM Alliances');

//Build all of the urls and hold them in memory
for($i = 0; $i < $maxAllianceId; $i++) {
    $urls[$i] = 'https://esi.tech.ccp.is/latest/alliances' . $Alliances[$i]['AllianceID'] . '/corporations/?datasource=tranquility';
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
        foreach($results[$j] as $res) {
            $found = $db->fetchColumn('SELECT CorporationID FROM Corporations WHERE CorporationID= :id', array('id' => $res));
            if($found == false) {
                $db->insert('Corporations', array('AllianceID' => $Alliances[$allianceIndex]['AllianceID'], 'CorporationID' => $res));
            }
        }
        
        //Update the last row worked on
        $row = ($i * $maxEntities) + $j;
        $nextRow = $row + 1;
        if($row == $maxAllianceId) {
            $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationIdBuild' => 1));
        } else {
            $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationIdBuild' => $nextRow));
        }
        $db->insert('ESILogs', array(
            'Time' => gmdate('d.m.Y H:i'),
            'Type' => 'BuildCorp',
            'Call' => 'buildcorporationidesi.php', 
            'Entry' => 'Corporations for Alliance: ' . $Alliances[$row]['AllianceID'] . 'built.'
        ));
        
        if($DEBUG == true) {
            printf("ID: " . $row . "\n");
            printf("Alliance ID: " . $Alliances[$row]['AllianceID'] . "\n");            
            $now = time();
            $seconds = $now - $start;
            printf("Time Taken: " . $seconds . " seconds.\n");
        }
    }
}

//After all calls are done, then close the database connection
DBClose($db);

?>
