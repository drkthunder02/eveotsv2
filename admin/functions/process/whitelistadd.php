<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function WhiteListAdd($allyName, $corpName, $characterName, $type) {
    if($type == "alliance") {
        $entityeType = 3;
    } else if ($type == "corp") {
        $entityType = 2;
    } else if($type == "char") {
        $entityType = 1;
    }
    //Get the EVEOTS configuration so we can call the config parameters for client_id and secretkey for ESI
    $conf = new EVEOTS\Config\Config();
    $config = $conf->GetESIConfig();
    
    $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
        'client_id' => $config['clientid'],
        'secret' => $config['secretkey']
    ]);
    $esi = new \Seat\Eseye\Eseye($authentication);

    //Open the database connection
    $db = DBOpen();
    if($type == "alliance") {
        $id = $db->fetchColumn('SELECT AllianceID FROM Alliances WHERE Alliance= :name', array('name' => $allyName));
        $list = $db->fetchRowMany('SELECT * FROM Blues');
        foreach($list as $li) {
            if($li['Entity'] == $allyName) {
                //If we have found it already in the blue list don't add it
                return;
            }
        }
        //If the alliance is found in the database, enter it into the Blues list,
        //otherwise search on ESI API for the name
        if($id == true) {
            $db->insert('Blues', array('EntityID' => $id, 'EntityType' => 3));
        } else {
            //Try to get the character info from ESI API
            try {
                $alliances = $esi->invoke('get', '/alliances/names/');
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                // The HTTP Response code and message can be retreived
                // from the exception...
                print $e->getCode() . PHP_EOL;
                print $e->getMessage() . PHP_EOL;
            }
            foreach($alliances as $alliance) {
                if($allyName == $alliance['alliance_name']) {
                    $db->insert('Blues', array('EntityID' => $alliance['alliance_id'], 'EntityType' => 3));
                    break;
                }
            }
        }
    }
    if($type == "corp") {
        $id = $db->fetchColumn('SELECT CorporationID FROM Corporations WHERE Corporatione= :name', array('name' => $corpName));
        $list = $db->fetchRowMany('SELECT * FROM Blues');
        foreach($list as $li) {
            if($li['Entity'] == $corpName) {
                //If we have found it already in the blue list don't add it
                return;
            }
        }
        //If the alliance is found in the database, enter it into the Blues list,
        //otherwise search on ESI API for the name
        if($id == true) {
            $db->insert('Blues', array('EntityID' => $id, 'EntityType' => 2));
        } else {
            //Try to get the character info from ESI API
            try {
                $corporations = $esi->invoke('get', '/corporations/names/');
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                // The HTTP Response code and message can be retreived
                // from the exception...
                print $e->getCode() . PHP_EOL;
                print $e->getMessage() . PHP_EOL;
            }
            foreach($corporations as $corporation) {
                if($corpName == $corporation['corporation_name']) {
                    $db->insert('Blues', array('EntityID' => $corporation['corporation_id'], 'EntityType' => 2));
                    break;
                }
            }
        }
    }
    if($type == "char") {
        $id = $db->fetchColumn('SELECT CharacterID FROM Characters WHERE Character= :name', array('name' => $charName));
        $list = $db->fetchRowMany('SELECT * FROM Blues');
        foreach($list as $li) {
            if($li['Entity'] == $charName) {
                //If we have found it already in the blue list don't add it
                return;
            }
        }
        //If the alliance is found in the database, enter it into the Blues list,
        //otherwise search on ESI API for the name
        if($id == true) {
            $db->insert('Blues', array('EntityID' => $id, 'EntityType' => 1));
        } else {
            //Try to get the character info from ESI API
            try {
                $characters = $esi->invoke('get', '/characters/names/');
            } catch (\Seat\Eseye\Exceptions\RequestFailedException $e) {
                // The HTTP Response code and message can be retreived
                // from the exception...
                print $e->getCode() . PHP_EOL;
                print $e->getMessage() . PHP_EOL;
            }
            foreach($characters as $character) {
                if($characterName == $character['character_name']) {
                    $db->insert('Blues', array('EntityID' => $character['character_id'], 'EntityType' => 1));
                    break;
                }
            }
        }
    }
    
    //Close the database connection
    DBClose($db);
}