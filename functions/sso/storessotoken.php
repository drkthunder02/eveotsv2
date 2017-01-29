<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function StoreSSOToken($characterID, $accessToken, $refreshToken) {
    //Open a connection to the database
    $db = DBOpen();
    
    $db->update('SSOTokens', array('CharacterID' => $characterID), array('AccessToken' => $accessToken, 'RefreshToken' => $refreshToken));
    
    //Close the connection to the database
    DBClose($db);
}