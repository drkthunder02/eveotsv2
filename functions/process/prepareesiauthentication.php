<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function PrepareESIAuthentication() {
    $config = parse_ini_file('esi.ini');
    
    $authentication = new \Seat\Eseye\Containers\EsiAuthentication([
        'client_id' => $config['client_id'],
        'secret' => $config['secret'],
        'access_token' => $config['access_token'],
        'refresh_token' => $config['refresh_token'],
    ]);
    
    return $authentication;
}