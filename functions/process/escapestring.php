<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function EscapeString($data) {
    
    $dbInfo = parse_ini_file(__DIR__.'/../configuration/database.ini');
    
    $dsn = 'mysql:' . $dbInfo['database'] . ':host=' . $dbInfo['server'];
    //$dsn = 'mysql:dbname=testdb;host=127.0.0.1';
    $username = $dbInfo['username'];
    $password = $dbInfo['password'];
    
    $pdo = new PDO($dsn, $username, $password);
    
    return $data;
}