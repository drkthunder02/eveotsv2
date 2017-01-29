<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//Start a session
$session = new \Custom\Session\Sessions();

//Esnure we have an action
if(!isset($_GET['action'])) {
    RedirectToNew();
}

//Decide where to go based on the value of 'action'
switch($_GET['action']) {
    case 'new':
        $_SESSION['test'] = 'bob';
        NewLogin();
        break;
    
    case 'submitcharacter':
        //Set the state session variable to be a unique identifier
        $_SESSION['state'] = uniqid();
        break;
    
    case 'eveonlinecallback':
        //Verify the state
        if($_REQUEST['state'] != $_SESSION['state']) {
            printf("Invalid State!  You will have to start again!<br>");
            printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?action=new\">Start again</a>");
            die();
        }
        
        //Clear the state value
        $_SESSION['state'] = NULL;
        
        //Prep the authentication header
        $headers = [
            'Authorization: Basic ' . base64_encode($_SESSION['clientid'] . ':' . $_SESSION['secret']),
            'Content-Type: application/json',
        ];
        
        //Seems like CCP does not mind JSON in the body
        $fields = json_encode([
            'grant_type' => 'authorization_code',
            'code'       => $_REQUEST['code'],
        ]);
        
        //Start a curl session
        $ch = curl_init('https://login.eveonline.com/oauth/token');
        curl_setopt_array($ch, [
                CURLOPT_URL             => 'https://login.eveonline.com/oauth/token',
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => $fields,
                CURLOPT_HTTPHEADER      => $headers,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_USERAGENT       => 'eseye/tokengenerator',
                CURLOPT_SSL_VERIFYPEER  => true,
                CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
            ]
        );
        
        $result = curl_exec($ch);
        $data = json_decode($reult);
        StoreSSOToken($data->character_id, $data->access_token, $data->refresh_token);
        
        break;
    //If we don't know what 'action' to perform then redirect to the start    
    default:
        RedirectToNew();
        break;
}