<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function CheckBlueStatus($Blues, $usWhiteList, $characterId, $corporationId) {
    $remove = true;
    
    foreach($Blues as $blue) {
        if(($char['CharacterID'] == $blue['EntityID']) || ($char['CharacterID'] == $usWhiteList)) {
            $remove = false;
        } else if(($corp['CorporationID'] == $blue['EntityID']) || ($char['CharacterID'] == $usWhiteList)) {
            $remove = false;
        } else if(($corp['AllianceID'] == $blue['EntityID']) || ($char['CharacterID'] == $usWhiteList)) {
            $remove = false;
        } else {
            $remove = true;
        }
    }
    
    return $remove;
}

