<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function SQLCheck($checkThis, $me) {
    $SQL = array(";", "=", '"', "'");
    foreach($SQL as $scanForThis) {
        $SQLcheck = strpos($checkThis, $scanForThis);
        if($SQLcheck !== false) {
            die("Error: You have entered an illegal character in " . $me . " (" . $scanForThis . "). [F" . __LINE__ . "]");
        }
    }
}