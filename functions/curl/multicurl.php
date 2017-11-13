<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function MultipleCURLRequest($data, $useragent, $options = array()) {
    //Array of cURL handles
    $curls = array();
    
    //Data to be returned
    $result = array();
    $results = array();
    
    //Multi cURL handle
    $mh = curl_multi_init();
    
    $header = 'Accept: application/json';
    
    //Loop through the $data and create curl handles
    //then add them to the mutli-handle for curl
    foreach($data as $key => $value) {
        $curls[$key] = curl_init();
        $url = $value;
        curl_setopt($curls[$key], CURLOPT_URL, $url);
        curl_setopt($curls[$key], CURLOPT_USERAGENT, $useragent);
        curl_setopt($curls[$key], CURLOPT_HTTPHEADER, array($header));
        curl_setopt($curls[$key], CURLOPT_HTTPGET, true);
        curl_setopt($curls[$key], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curls[$key], CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curls[$key], CURLOPT_SSL_VERIFYHOST, 2);
        
        //Extra Options
        if(!empty($options)) {
            curl_setopt_array($curls[$key], $options);
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
    
    //Decode each result in its own array of arrays
    foreach($result as $info => $mined) {
        $results[$info] = json_decode($mined, true); 
    }
    
    //Get the contet and remove the handles
    //foreach($curls as $key => $value) {
    //    $result[$key] = json_decode(curl_multi_getcontent($value), true);
    //    curl_multi_remove_handle($mh, $value);
    //}
    
    //Once all the calls are completed close the multi curl channel
    curl_multi_close($mh);
    
    return $results;
}