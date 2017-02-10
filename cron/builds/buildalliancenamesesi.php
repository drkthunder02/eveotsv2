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
$DEBUG = $config->GetDebugMode();

//Open the database connection
$db = DBOpen();

//Get the last alliance id worked on
$nextAllianceId = $db->fetchColumn('SELECT NextAllianceNameBuild FROM ESICallsAlliance WHERE id= :id', array('id' => 1));
$maxAllianceId = $db->fetchColumN('SELECT COUNT(id) FROM Alliances');

//Get all of the alliances 
$Alliances = $db->fetchRowMany('SELECT * FROM Alliances');

//Cycle through all of the alliances to check for blanks
for($row = $nextAllianceId; $row <= $maxAllianceId - 1; $row++) {
    $temp = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $Alliances[$row]['AllianceID']));
    if($temp['Alliance'] == "" || $temp['Ticker'] == "") {
        $url = 'https://esi.tech.ccp.is/latest/alliances/' . $Alliances[$row]['AllianceID'] . '/?datasource=tranquility';
        $header = 'Accept: application/json';
        $useragent = 'EVEOTSv2 Auth';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        
        //If a curl error happens, print it out, then die.
        if(curl_error($ch)) {
            printf("Curl Error: " . curl_error($ch) . "\n");
            die();
        } else {
            //If no curl error, then continue with the programming
            $allianceEsi = json_decode($result, true);
            $db->update('Alliances', array('AllianceID' => $temp['AllianceID']), array('Alliance' => $allianceEsi['alliance_name'], 'Ticker' => $allianceEsi['ticker']));
            
            //Close the curl channel
            curl_close($ch);
            //Print out debug info if necessary
            if($DEBUG == true) {
                var_dump($allianceEsi);
                $now = time();
                $seconds = $now - $start;
                printf("Time Taken: " . $seconds . " seconds.\n");
            }
            //Update the last row worked on
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
                'Entry' => 'Alliance of name ' . $allianceEsi['alliance_name'] . ' added to the database.'
            ));
            
        }    
        
        //Sleetp for 2 seconds to prevent ESI lock
        //sleep(2);
    }
}

//Close the database connection
DBClose($db);

?>
