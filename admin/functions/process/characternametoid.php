<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Using the character id get the name as it appears in game
function CharacterNameToID(\Simplon\Mysql\Mysql $db, \Seat\Eseye\Eseye $esi, \Seat\Eseye\Exceptions $log, $id) {
    //First we are going to search the database of characters we have to see if we have the id on file already
    $name = $db->fetchColumn('SELECT Character FROM Members WHERE CharacterID= :id', array('id' => $id));
    if($name != NULL) {
        return $name;
    }
    //If we have not received the name from the database let's go to the ESI API for the name
    try {
        $results = $esi->invoke('get', '/character/{character_id}/', [
            'character_id' => (int)$id,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        $errorCode = $e->getCode();
        $errorMsg = $e->getMessage();
        $error = "Code: " . $errorCode . ", Message: " . $errorMsg . "\n";
        //Print out the error to the log
        $log->error($error);
    }
    
    $name = $results->character_name;
    //We also need the corporation name for the database entry
    try {
        $corpInfo = $esi->invoke('get', 'corporation/{corporation_id}/', [
            'corporation_id' => $results->corporation_id,
        ]);
    } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
        $errorCode = $e->getCode();
        $errorMsg = $e->getMessage();
        $error = "Code: " . $errorCode . ", Message: " . $errorMsg . "\n";
        //Print out the error to the log
        $log->error($error);
    }
    //Now that we have the character name let's add them to the database for quicker lookups
    $db->replace('Members', array(
       'Corporation' => $corpInfo->corporation_name,
        'CorporationID' => $corpInfo->corporation_id,
        'Character' => $name,
        'CharacterID' => $id        
    ));
    
    
    //Return the name
    return $name;
}