<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/../../functions/registry.php';

$db = DBOpen();

$db->executeSql('TRUNCATE Logs');

DBClose($db);

//Redirect back to the admin panel
$location = ServerProtocol() . $_SERVER['HTTP_HOST'];
$location = $location . dirname($_SERVER['PHP_SELF']) . '/../../admin_panel.php?msg=WhiteListSuccess';
header("Location: $location");
