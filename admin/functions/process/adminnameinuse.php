<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminNameInUse(\Simplon\Mysql\Mysql $db, $adminUsername) {
    $result = $db->fetchColumn('SELECT username FROM Admins WHERE username= :user', array('user' => $adminUsername));
    //If we get a result, then the name is in use
    if($result != NULL) {
        return true;
    } else {
        return false;
    }
}