<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

/*
 * This file will get 100 alliances at a time, and check their information against ESI
 */

require_once __DIR__.'/../functions/registry.php';

$start = time();

//Open the database connection
$db = DBOpen();
//Get the next alliance id to check
$nextAllianceRow = $db->fetchColumn('SELECT NextAllianceIdCheck FROM ESICallsAlliance');
$maxAllianceRow = $db->fetchColumn('SELECT COUNT(id) FROM Alliances');

$configClass = new \EVEOTS\Config\Config();
$config = $configClass->GetESIConfig();

$esiCall = new \EVEOTS\ESI\ESI($config['useragent'], $config['clientid'], $config['secretkey']);

for($row = $nextAllianceRow; $row <= $maxAllianceRow; $row++) {
     //Get the alliance information from the database
    $allianceDB = $db->fetchRow('SELECT * FROM Alliances WHERE id= :id', array('id' => $row));
    $Alliance = $esiCall->GetAllianceInfo($allianceDB['AllianceID']);
    if($Alliance == null) {
        die();
    } else {
        if(($allianceDB['Alliance'] != $Alliance['alliance_name'] || $allianceDB['Ticker'] != $Alliance['ticker']) && !isset($Alliance['error'])) {
            $db->update('Alliances', array('AllianceID' => $allianceDB['AllianceID']), array(
                'Alliance' => $allianceEsi['alliance_name'],
                'Ticker' => $allianceEsi['ticker']
            ));
        }
        //Update the last row modified for ESI
        $nextRow = $row + 1;
        if($row == $maxAllianceRow) {
            $db->update('ESICallsAlliance', array('id' => 1), array('NextAllianceIdCheck' => 0));
        } else {
            $db->update('ESICallsAlliance', array('id' => 1), array('NextAllianceIdCheck' => $nextRow));
        }
    }
    
}

//Close the database connection after the script is completed
DBClose($db);

?>
