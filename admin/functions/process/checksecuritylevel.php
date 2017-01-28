<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function CheckSecurityLevel(\Simplon\Mysql\Mysql $db, $username) {
    $results = $db->fetchRow('SELECT id,securityLevel FROM Admins WHERE username= :user', array('user' => $username));
    $data = array(
        'SecurityLevel' => $results['securityLevel'],
        'SecurityID' => $results['id'],
    );
    
    return $data;
}