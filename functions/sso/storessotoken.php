<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function StoreSSOToken($accessToken, $refreshToken, $clientid, $secretkey) {
    //Make a curl call to the eve online servers to get the character id
    //Do the initial check.
    $url = 'https://login.eveonline.com/oauth/verify';
    $header='Authorization: Bearer ' . $accessToken;
    $useragent = 'W4RP EVEOTSv2 Auth';  
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    //Get the result from the curl call
    $result = curl_exec($ch);
    if ($result===false) {
        printf("Error with talking to eveonline servers.<br>");
    }
    //Close the curl channel
    curl_close($ch);
    //Decode the json response
    $data=json_decode($result);
    
    //Get the resultant data from the curl call
    $data = json_decode($result);
    $characterID = $data->CharacterID;
    
    //Open a connection to the database
    $db = DBOpen();
    //Insert the data into the SSOTokens table.  Use replace as it tries to replace, then insert into the database if the replace doesn't work.
    $db->replace('SSOTokens', array('CharacterID' => $characterID, 'AccessToken' => $accessToken, 'RefreshToken' => $refreshToken));
    
    //Close the connection to the database
    DBClose($db);
    //Store the character info in the character table of the database
    StoreCharacterInfo($characterID, $accessToken, $refreshToken);
}