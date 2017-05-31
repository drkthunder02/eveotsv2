<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function ServerProtocol() {
    if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        return 'https://';
    } else {
        return 'http://';
    }
}