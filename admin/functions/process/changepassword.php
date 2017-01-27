<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function ChangePassword(\Simplon\Mysql\Mysql $db, $newPassword, $sid, $username) {
    printf("<div class=\"container\">
                Changing password...<br>");
    $db->update('Admins', array('id' => $sid), array('password' => $newPassword));
    //Enter into the log
    $timestamp = gmdate('d.m.Y H:i');
    $log = $username . " changed their password.";
    $db->insert('Logs', array('time' => $timestamp, 'entry' => $log));
    printf("Your password has been changed.<br></div>");
}

?>
