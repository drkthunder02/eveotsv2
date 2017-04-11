<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

require_once __DIR__.'/../functions/registry.php';

$start = time();
//Open the database connection
$db = DBOpen();
//Get the next corporation id to check
$nextCorpRow = $db->fetchColumn('SELECT NextCorporationIdCheck FROM ESICalls');
$maxCorpRow = $db->fetchColumn('SELECT COUNT(id) FROM Corporations');

$configClass = new \EVEOTS\Config\Config();
$config = $configClass->GetESIConfig();

$esiCall = new \EVEOTS\ESI\ESI($config['useragent'], $config['clientid'], $config['secretkey']);

for($row = $nextCorpRow; $row <= $maxCorpRow; $row++) {
    //Get the corporation info from the database
    $corpDB = $db->fetchRow('SELECT * FROM Corporations WHERE id= :id', array('id' => $row));
    $Corporation = $esiCall->GetCorporationInfo($corpDB['CorporationID']);
    if($Corporation == null) {
        die();
    } else {
        if(($corpDB['AllianceID'] != $Corporation['alliance_id'] || $corpDB['MemberCount'] != $Corporation['member_count']) && !isset($Corporation['error'])) {
            $db->update('Corporations', array('CorporationID' => $corpEsi['corporation_id']), array(
                'AllianceID' => $corpEsi['alliance_id'],
                'MemberCount' => $corpEsi['member_count'],
                'Corporation' => $corpEsi['corporation_name'],
                'Ticker' => $corpEsi['ticker']
            ));
        }
        //Update the last row modified for ESI
        $nextRow = $row + 1;
        if($row == $maxAllianceRow) {
            $db->update('ESICalls', array('id' => 1), array('NextCorporationIdCheck' => 1));
        } else {
            $db->update('ESICalls', array('id' => 1), array('NextCorporationIdCheck' => $nextRow));
        }
    }
}

//Close the database connection
DBClose($db);

?>
