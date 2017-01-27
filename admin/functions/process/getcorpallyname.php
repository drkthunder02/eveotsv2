<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function GetCorpAllyName(\Simplon\Mysql $db, \Seat\Eseye\Eseye $esi, \Seat\Eseye\Log $log, $corpID) {
    //Declare variables with NULL data
    $corpName = "";
    $allyName = "";

    //Attempt to get the corporation from the database before ESI
    $corporation = $db->fetchRow('SELECT * FROM Corporations WHERE CorporationID= :id', array('id' => (string)$corpID));
    $rowsCorp = $db->getRowCount();
    //If we get a row from the database, then use it rather than ESI
    if($rowsCorp > 0) {
        $corpName = $corporation['Corporation'];
        if($corporation['AllianceID'] > 0) {
            $alliance = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $corporation['AllianceID']));
            $allyName = $alliance['Alliance'];
        }
    } else {
        //Attempt to get the corporation name from ESI API since it was not received from the database
        try {
           $corporation = $esi->invoke('get', '/corporation/{corporation_id}/', [
                'corporation_id' => (int)$corpID,
            ]); 
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {        
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();
            $error = "Code: " . $errorCode . ", Message: " . $errorMsg . "\n";
            //Print out the error to the log
            $log->error($error);
        }
        $corpName = $corporation->corporation_name;
        //Update the database with the corporation information
        $db->replace('Corporations', array(
            'AllianceID' => $corporation->alliance_id,
            'Corporation' => $corporation->corporation_name,
            'CorporationID' => $corporation->corporation_id,
            'MemberCount' => $corporation->member_count,
        ));
        $allianceID = $corporation->alliance_id;
        //Attempt to get the alliance name from ESI API since we have completed the fetch for the corporation
        try {
            $alliance = $esi->invoke('get', 'alliance/{alliance_id}/', [
                'alliance_id' => (int)$allianceID,
            ]);
        } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {        
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();
            $error = "Code: " . $errorCode . ", Message: " . $errorMsg . "\n";
            //Print out the error to the log
            $log->error($error);
        }
        //Update the database with the alliance information
        $db->replace('Alliances', array(
            'Alliance' => $alliance->alliance_name,
            'AllianceID' => $alliance->alliance_id,
            'Ticker' => $alliance->ticker,
        ));
        $allyName = $alliance->alliance_name;
    }
    
    //Populate the data array with the data received from the DB or ESI API
    $data = array(
        'corpName' => $corpName,
        'allianceName' => $allyName,
    );
    
    return $data;
}