<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

function SQLCheckNames($checkThis,$me) {
    // this check allows ' which is legally in some names so will be dealt with just prior to storage with a substitue
    $SQL = array(";","=",'"');
    foreach ($SQL as $scanForThis) {
        $SQLcheck = strpos($checkThis, $scanForThis);
        if ($SQLcheck !== false) {
                die("Error: You have entered an illegal character in ".$me." (".$scanForThis."). [F".__LINE__."]");
        }
    }
}