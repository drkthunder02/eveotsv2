<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function BuildCorporationIdESI($idFrom, $idTo) {
    $start = time();
    
    //Get the debug scope from the configuration file
    $config = new \EVEOTS\Config\Config();
    $esi = $config->GetESIConfig();
    $useragent = $esi['useragent'];
    $DEBUG = $config->GetDebugMode();
    
    //Open the database connection
    $db = DBOpen();
    
    $Alliances = $db->fetchRowMany('SELECT * FROM Alliances WHERE id>= :idFrom AND id< :idTo', array('idFrom' => $idFrom, 'idTo' => $idTo));
    $count = $idTo - $idFrom;
    for($row = $idFrom; $row < $count; $row++) {
        $id = $Alliances[$row]['AllianceID'];
        //Build our cURL call
        $url = 'https://esi.tech.ccp.is/latest/alliances/' . $id . '/corporations/?datasource=tranquility';
        $header = 'Accept: application/json';
        $ch = curl_init();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        //If there is a cURL error, then print out the error, otherwise process the data
        if(curl_error($ch)) {
            printf("Curl Error: " . curl_error($ch) . "<br>");
            die();
        } else {
            //Get the data into an array format from the json data
            $corpEsi = json_decode($result, true);
            foreach($corpEsi as $corpId) {
                $found = $db->fetchColumn('SELECT CorporationID FROM Corporations WHERE CorporationID= :id', array('id' => $corpId));
                if($found == false) {
                    $db->insert('Corporations', array('AllianceID' => $Alliances[$row]['AllianceID'], 'CorporationID' => $corpId));
                }
            }
            //Insert a log entry into the logs
            $db->insert('ESILogs', array('Type' => 'BuildCorp', 'Call' => 'buildcorporationidesi.php', 'Entry' => 'Corporations for Alliance: ' . $Alliances[$row]['AllianceID'] . 'built.'));

            if($DEBUG == true) {
                printf("ID: " . $row . "\n");
                printf("Alliance ID: " . $Alliances[$row]['AllianceID'] . "\n");            
                $now = time();
                $seconds = $now - $start;
                printf("Time Taken: " . $seconds . " seconds.\n");
            }
        }

        //Close the curl channel to reset it
        curl_close($ch);
    }
    
    //After all calls are done, then close the database connection
    DBClose($db);
}

?>
