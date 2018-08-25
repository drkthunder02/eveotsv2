<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function CheckBlueStatus($Blues, $usWhiteList, $characterId, $corporationId, $allianceId) {
    $remove = true;
    
    foreach($Blues as $blue) {
        if(($characterId == $blue['EntityID']) || ($characterId == $usWhiteList)) {
            $remove = false;
            break;
        } else if(($corporationId == $blue['EntityID']) || ($corporationId == $usWhiteList)) {
            $remove = false;
            break;
        } else if(($allianceId == $blue['EntityID']) || ($allianceId == $usWhiteList)) {
            $remove = false;
            break;
        } else {
            $remove = true;
        }
    }
    
    return $remove;
}

