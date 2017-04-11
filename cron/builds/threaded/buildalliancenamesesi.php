<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function BuildAllianceNamesESI($idFrom, $idTo) {
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
    for($row = 0; $row < $count; $row++) {
        $temp = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $Alliances[$row]['AllianceID']));
        if($temp['Alliance'] == "" || $temp['Ticker'] == "") {
            $url = 'https://esi.tech.ccp.is/latest/alliances/' . $Alliances[$row]['AllianceID'] . '/?datasource=tranquitlity';
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
                //Insert a log entry into 
                $db->insert('ESILogs', array('Type' => 'BuildAlliances', 'Call' => 'buildalliancenamesesi.php', 'Entry' => 'Alliance of name ' . $allianceEsi['alliance_name'] . ' added to the database.'));

            }
        }
    }
    
    DBClose($db);
}

?>
