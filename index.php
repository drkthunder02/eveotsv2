<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// 1 - https://login.eveonline.com/oauth/authorize
// 2 - https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri=https%3A%2F%2F3rdpartysite.com%2Fcallback&client_id=3rdpartyClientId&scope=characterContactsRead%20characterContactsWrite&state=uniquestate123
// 3 - Verify the Authorization Code and retrieve the access token and refresh token
// 4 - Store the access token and refresh token in the database

require_once __DIR__.'/functions/registry.php';

//Start a session
$session = new \Custom\Session\Sessions();
printf("Started a session.<br>");
//Get the configuration data from the class
$config = new \EVEOTS\Config\Config();
printf("Got the configuration.<br>");
$esiconfig = $config->GetESIConfig();
printf("Got the ESI Configuration.");
$clientid = $esiconfig['clientid'];
$secretkey = $esiconfig['secretkey'];
printf("Loaded the clientid and secretkey,<br>");

//If the state is not set then set it to NULL
if(!isset($_SESSION['state'])) {
    $_SESSION['state'] = uniqid();
}

PrintHTMLHeader();

switch($_REQUEST['action']) {
    //If we are the start of the SSO process, then print a box to login into EVE via the SSO
    case 'new':
        //https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri=https%3A%2F%2F3rdpartysite.com%2Fcallback&client_id=3rdpartyClientId&scope=characterContactsRead%20characterContactsWrite&state=uniquestate123
        printf("<div class=\"container\">");
        printf("<div class=\"jumbotron\">");
        printf("<a href=\"https://login.eveonline.com/oauth/authorize/?response_type=code&redirect_uri=\"" . 
                urldecode(GetSSOCallbackURL()) . "&client_id=" . 
                $clientID . "&scope=publicData" . "&state" . $_SESSION['state'] . ">");
        printf("<img src=\"images/EVE_SSO_Login_Buttons_Large_Black.png\">");
        printf("</a>");
        printf("</div>");
        printf("</div>");
        break;
    //If we got the redirect back to the site, then verify the tokens, and store them
    case 'eveonlinecallback':
        //Verify the state
        if($_REQUEST['state'] != $_SESSION['state']) {
            printf("<div class=\"container\">");
            printf("Invalid State!  You will have to start again.");
            printf("<a href=\"" . $_SERVER['PHP_SELF'] . "?action=new\">Start again!</a>");
            printf("</div>");
            die();
        }
        
        //Clear the state value.
        $_SESSION['state'] = NULL;
        //Prep the authorization header.
        $headers = [
            'Authorization: Basic ' . base64_decode($clientid . ":" . $secretkey),
            'Content-Type: application/json',
        ];
        //Prep the fields for the curl call
        $fields = ([
            'grent_type' => 'authorization_code',
            'code' => $_REQUEST['code'],
        ]);
        //Start a curl session
        $ch = curl_init("https://login.eveonline.com/oauth/token");
        //Set the curl options
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://login.eveonline.com/oauth/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'EVEOTSV2',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_CIPER_LIST => 'TLSv1',
        ]);
        //Execute the curl call
        $result = curl_exec($ch);
        //Get the resultant data from the curl call
        $data = json_decode($result);
        //With the access token, and refresh token, store it in the database
        StoreSSOToken($data->access_token, $data->refresh_token, $clientid, $secretkey);
        PrintSSOSuccess();
        break;
    //If we don't know what state we are in then go back to the beginning
    default:
        RedirectToNew();
        break;
}

