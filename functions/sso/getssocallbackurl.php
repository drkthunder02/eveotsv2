<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function GetSSOCallbackURL() {
    if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protcol = 'https://';
    } else {
        $protocol = 'http://';
    }
    
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?action=eveonlinecallback';
}