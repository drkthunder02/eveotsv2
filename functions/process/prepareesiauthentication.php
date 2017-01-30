<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrepareESIAuthentication($characterID) {
    $config = parse_ini_file('/../configuration/esi.ini');
    
    $db = DBOpen();
    $esiInfo = $db->fetchRow('SELECT * FROM SSOTokens WHERE CharacterID= :char', array('char' => $characterID));
    DBClose($db);
        
    $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
        'client_id' => $config['client_id'],
        'secret' => $config['secret'],
        'access_token' => $esiInfo['AccessToken'],
        'refresh_token' => $esiInfo['RefreshToken'],
    ]);
    
    return $authentication;
}