<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AddLogEntry(\Simplon\Mysql\Mysql $db, $timestamp, $entry) {
    $db->insert('Logs', array(
        'time' => $timestamp,
        'entry' => $entry,
    ));
}