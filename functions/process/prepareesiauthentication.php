<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrepareESIAuthentication($characterID) {
    //Read configuration parameters from the ini file to pre
    $conf = new EVEOTS\Config\Config();
    $config = $conf->GetESIConfig();
    
    $db = DBOpen();
    $esiInfo = $db->fetchRow('SELECT * FROM SSOTokens WHERE CharacterID= :char', array('char' => $characterID));
    DBClose($db);
        
    $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
        'client_id' => $config['clientid'],
        'secret' => $config['secretkey'],
        'access_token' => $esiInfo['AccessToken'],
        'refresh_token' => $esiInfo['RefreshToken'],
    ]);
    
    return $authentication;
}