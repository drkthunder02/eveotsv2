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

$config = new \EVEOTS\Config\Config();
$esi = $config->GetESIConfig();
$useragent = $esi['useragent'];

for($row = $nextAllianceRow; $row <= $maxAllianceRow; $row++) {
     //Get the alliance information from the database
    $allianceDB = $db->fetchRow('SELECT * FROM Alliances WHERE id= :id', array('id' => $row));
    //Let's attempt our ESI Call to the ESI API to get the data for the alliance
    $url = 'https://esi.tech.ccp.is/latest/alliances/' . $allianceDB['AllianceID'] . '/?datasource=tranquility';
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
        die();
    } else {
        //Decode the json data into an array
        $allianceEsi = json_decode($result, true);
        //Update the database if necessary
        if(($allianceDB['Alliance'] != $allianceEsi['alliance_name'] || $allianceDB['Ticker'] != $allianceEsi['ticker']) && !isset($allianceEsi['error'])) {
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
    
    //Close the curl channel to reset it
    curl_close($ch);
}

//Close the database connection after the script is completed
DBClose($db);

?>
