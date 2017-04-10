<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

/*
 * This file will check the corporations until either an ESI error occurs
 * or all of the corporations are checked
 */

require_once __DIR__.'/../functions/registry.php';

$start = time();
//Open the database connection
$db = DBOpen();
//Get the next corporation id to check
$nextCorpRow = $db->fetchColumn('SELECT NextCorporationIdCheck FROM ESICalls');
$maxCorpRow = $db->fetchColumn('SELECT COUNT(id) FROM Corporations');

$config = new \EVEOTS\Config\Config();
$esi = $config->GetESIConfig();
$useragent = $esi['useragent'];

for($row = $nextCorpRow; $row <= $maxCorpRow; $row++) {
    //Get the corporation info from the database
    $corpDB = $db->fetchRow('SELECT * FROM Corporations WHERE id= :id', array('id' => $row));
    //Build the ESI Call for the ESI API
    $url = 'https://esi.tech.ccp.is/latest/alliances/' . $corpDB['CorporationID'] . '/?datasource=tranquility';
    $header = 'Accept: application/json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    $result = curl_exec($ch);
    //If there is a cURL error, then error out of the script
    if(curl_error($ch)) {
        printf("Curl Error: " . curl_error($ch) . "\n");
        die();
    } else {
        //Decode the json data into an array
        $corpEsi = json_decode($result, true);
        //Update the database as necessary
        if(($corpDB['AllianceID'] != $corpEsi['alliance_id'] || $corpDB['MemberCount'] != $corpEsi['member_count']) && !isset($corpEsi['error'])) {
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
        
        //Close the curl channel to reset it
        curl_close($ch);
        //Sleep for 2 seconds
        sleep(2);
    }
}

//Close the database connection
DBClose($db);

?>
