<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function StoreSSOToken($accessToken, $refreshToken, $clientid, $secretKey) {
    //Make a curl call to the eve online servers to get the character id
    //Start a curl session
    $ch = curl_init("https://login.eveonline.com/oauth/verify");
    $headers = [
        'Authorization: Basic ' . base64_decode($clientid . ":" . $secretkey),
        'Content-Type: application/json',
    ];
    curl_setop_array($ch, [
        CURLOPT_URL => 'https://login.eveonline.com/oauth/verify',
        CURLOPT_POST => true,
        CURLOPT_POST => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'EVEOTSV2',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
    ]);
    //Execute the curl call
    $result = curl_exec($ch);
    //Get the resultant data from the curl call
    $data = json_decode($result);
    $characterID = $data->CharacterID;
    
    //Open a connection to the database
    $db = DBOpen();
    //Insert the data into the SSOTokens table.  Use replace as it tries to 
    $db->replace('SSOTokens', array('CharacterID' => $characterID, 'AccessToken' => $accessToken, 'RefreshToken' => $refreshToken));
    
    //Close the connection to the database
    DBClose($db);
    //Store the character info in the character table of the database
    StoreCharacterInfo($characterID, $accessToken, $refreshToken);
}