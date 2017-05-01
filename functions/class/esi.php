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
    
    public function GetESIInfo($id, $type) {
        $url = $this->BuildSingleUrl($type, $id);
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
            $data = json_decode($result, true);

            return $data;
        }
    }
    
    private function BuildSingleUrl($type, $id) {
        $firstPart = 'https://esi.tech.ccp.is/latest/';
        $lastPart = '/?datasource=tranquility';
        
        if($type == 'Character') {
            $url = $firstPart . 'characters/' . $value . $lastPart;
        } else if ($type == 'Corporation') {
            $url = $firstPart . 'corporations/' . $value . $lastPart;
        } else if ($type == 'Alliance') {
            $url = $firstPart . 'alliances/' . $value . $lastPart;
        }
        
        return $url;
    }
    
    public function GetInfoMulti($type, $data, $options = array()) {
        //Array of cURL handles
        $urls = array();
        $urls = $this->BuildMultiUrl($type, $data);
        
        //Data to be returned
        $result = array();
        $results = array();
        
        //Multi cURL handle
        $mh = curl_multi_init();
        
        //Loop through the $data and create the curl handles
        //then add them to the multi-handle for curl
        foreach($data as $key => $value) {
            $curls[$key] = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url[$key]);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            
            //Extra options
            if(!empty($options)) {
                curl_setopt($curls[$key], $options);
            }
            
            //Add the handles
            curl_multi_add_handle($mh, $curls[$key]);
        }
        
        //Execute the cURL handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while($running > 0);
        
        //Get the content and remove the handles
        foreach($curls as $key => $value) {
            $result[$key] = curl_multi_getcontent($value);
            curl_multi_remove_handle($mh, $value);
        }
        
        //Decode each result in its own array of array values
        foreach($result as $info => $mined) {
            $results[$info] = json_decode($mined, true);
        }
        
        //Once all the calls are completed close the multi curl channel
        curl_multi_close($mh);
        
        return $results;
    }
    
    private function BuildMultiUrl($type, $data) {
        $firstPart = 'https://esi.tech.ccp.is/latest/';
        $lastPart = '/?datasource=tranquility';
        
        $urls = array();
        
        if($type == 'Character') {
            foreach($data as $key => $value) {
                $urls[$key] = $firstPart . 'characters/' . $value . $lastPart;
            }
        } else if ($type == 'Corporation') {
            foreach($data as $key => $value) {
                $urls[$key] = $firstPart . 'corporations/' . $value . $lastPart;
            }
        } else if ($type == 'Alliance') {
            foreach($data as $key => $value) {
                $urls[$key] = $firstPart . 'alliances/' . $value . $lastPart;
            }
        }
        
        return $urls;
    }
}