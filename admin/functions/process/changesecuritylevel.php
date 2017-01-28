<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function ChangeSecurityLevel(\Simplon\Mysql\Mysql $db, $id, $newSL, $username) {
    $db->update('Admins', array('id' => $id), array('securityLevel' => $newSL));
    $timestamp = gmdate('d.m.Y H:i');
    $entry = $username . "'s security level has been changed.<br>";
    AddLogEntry($db, $timestamp, $entry);
}

?>