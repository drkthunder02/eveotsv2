<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function CheckLogin() {
    if(!isset($_SESSION['EVEOTSusername'])) {
        $username = "";
        header("location: index.php");
    } else {
        $username = $_SESSION['EVEOTSusername'];
    }
    
    return $username;
}