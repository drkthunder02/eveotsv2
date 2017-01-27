<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

// don't put html in a cron jobs output
function Format($php, $html) {
    if ($_SERVER['HTTP_USER_AGENT'] == false) {
        echo $php;
    } else {
        echo $html;
    }
}