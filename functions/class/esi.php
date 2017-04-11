<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

namespace EVEOTS\ESI;

class ESI {
    private $refreshToken;
    private $refreshTokenExpiry;
    private $accessToken;
    
    private $clientId;
    private $secretKey;
    private $userAgent;
    
    protected $esi = array(
        'scheme' => 'https',
        'host' => 'esi.tech.ccp.is',
        'path' => 'latest'
    );
    
    public function __construct($user, $client = null, $secret = null ) {
        $this->clientId = $client;
        $this->secretKey = $secret;
        $this->userAgent = $user;
    }
    
    public function GetTokens() {
        $data = array(
            'RefreshToken' => $this->refreshToken,
            'AccessToken' => $this->accessToken
        );
        
        return $data;
    }
    
    public function GetAccessToken($code) {
        $url = 'https://login.eveonline.com/oauth/token';
        $header = 'Authorization: Basic '.base64_encode($this->clientId.':'.$this->secretKey);
        $fields_string='';
        $fields = array(
                    'grant_type' => 'authorization_code',
                    'code' => $code
                );
        
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        
        rtrim($fields_string, '&');
        //Initialize the curl connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        
        //Get the resultant data from the curl call
        $data = json_decode($result, true);
        
        $this->refreshToken = $data['refresh_token'];
        $this->accessToken = $data['access_token'];
    }
    
    public function RefreshAccess() {
        $url = 'https://login.eveonline.com/oauth/token';
        $header = 'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->secretKey);
        $fields_string = '';
        $fields = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refreshToken
        );
        
        foreach($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        //Initialize the cURL connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        //Get the resultant data from the curl call in an array format
        $data = json_decode($result, true);
        //Modify the variables of the class
        $this->refreshToken = $data['refresh_token'];
        $this->refreshTokenExpiry = now() + $data['expires_in'];
        $this->accessToken = $data['access_token'];        
    }
    
    public function GetCharacterInfo($characterId) {
        $url = 'https://esi.tech.ccp.is/latest/characters/' . $characterId . '/?datasource=tranquility';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        //Check for a curl error
        if(curl_error($ch)) {
            return null;
        } else {
            curl_close($ch);
            $character = json_decode($result, true);

            return $character;
        }
    }
    
    public function GetCorporationInfo($corporationId) {
        $url = 'https://esi.tech.ccp.is/latest/corporations/' . $corporationId . '/?datasource=tranquility';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        if(curl_error($ch)) {
            return null;
        } else {
            curl_close($ch);
            $corporation = json_decode($result, true);

            return $corporation;
        }
    }
    
    public function GetAllianceInfo($allianceId) {
        $url = 'https://esi.tech.ccp.is/latest/alliances/' . $allianceId . '/?datasource=tranquility';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $result = curl_exec($ch);
        if(curl_error($ch)) {
            return null;
        } else {
            curl_close($ch);
            $alliance = json_decode($result, true);

            return $alliance;
        }
    }
    
    
}