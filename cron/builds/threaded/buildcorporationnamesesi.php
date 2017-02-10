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
$DEBUG = $config->GetDebugMode();

//Open a database connection
$db = DBOpen();

//Get the last corporation id worked on.
$nextCorpName = $db->fetchColumn('SELECT NextCorporationNameBuild FROM ESICallsCorporation WHERE id= :id', array('id' => 1));
$maxCorpName = $db->fetchColumn('SELECT COUNT(id) FROM Corporations');

//Build a set of corporations to check
$Corporations = $db->fetchRowMany('SELECT * FROM Corporations');

//Cycle through the corporations to update the data
for($row = $nextCorpName; $row <= $maxCorpName - 1; $row++) {
    $temp = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => $Corporations[$row]['CorporationID']));
    //Check the corporation
    if($temp['Corporation'] == "" || $temp['MemberCount'] == "" || $temp['Ticker'] == "") {
        //Build the curl call
        $url = 'https://esi.tech.ccp.is/latest/corporations/' . $temp['CorporationID'] . '/?datasource=tranquility';
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
        
        if(curl_error($ch)) {
            printf("Curl Error: " . curl_error($ch) . "<br>");
            die();
        } else {
            //Decode the json data into an array
            $corpEsi = json_decode($result, true);
            $db->update('Corporations', array('CorporationID' => $Corporations[$row]['CorporationID']), array(
                'Corporation' => $corpEsi['corporation_name'],
                'MemberCount' => $corpEsi['member_count'],
                'Ticker' => $corpEsi['ticker']
            ));
            
             //Update the last row worked on
            $nextRow = $row + 1;
            if($row == $maxCorpName) {
                $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationNameBuild' => 1));
            } else {
                $db->update('ESICallsCorporation', array('id' => 1), array('NextCorporationNameBuild' => $nextRow));
            }
            
            //Insert a new log entry into the database
            $db->insert('ESILogs', array(
                'Type' => 'BuildCorporation',
                'Call' => 'buildcorporationnamesesi.php',
                'Entry' => 'Corporation of name ' . $corpEsi['corporation_name'] . ' updated in the database.'
            ));
            
            //Display our debug information if debug is set
            if($DEBUG === true) {
                printf($temp['CorporationID'] . " of name " . $corpEsi['corporation_name'] . " update.\n");
                $now = time();
                $seconds = $now - $start;
                printf("Time Take: " . $seconds . " seconds.\n");
            } 
        }
        
        //Close the curl connection
        curl_close($ch);  
        
        //Sleep for 2 seconds
        //sleep(2);
    }
}

//Close the database connection
DBClose($db);

?>
