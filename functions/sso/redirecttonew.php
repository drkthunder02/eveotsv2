<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function RedirectToNew() {
    header('Location: ' . $_SERVER['PHP_SELF'] . '?action=new');
    die();
}

?>
