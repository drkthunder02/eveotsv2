<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminAdd(\Simplon\Mysql\Mysql $db, $name, $pass, $security) {
    if($name == "") {
        printf("Character Name cannot be blank.<br /><br />");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        return;
    } else if($pass == "") {
        printf("Character Name cannot be blank.<br /><br />");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        return;
    } else if($security == "") {
        printf("Character Name cannot be blank.<br /><br />");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        return;
    } else if($security != "1" && $security != "2") {
        printf("Character Name cannot be blank.<br /><br />");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        return;
    }
    // Make sure password is only a-z A-Z 0-9
    if(preg_match("/^[a-zA-Z0-9]+$/", $adminPassword) === 0) {
        printf("Error: Passwords can only contain A-Z, a-z and 0-9<br /><br />");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        return;
    }
    //Check if the Character Name is already in use
    $characterAdmin = $db->fetchRow('SELECT * FROM Admins WHERE username= :user', array('user' => $name));
    if($db->getRowCount() > 0) {
        printf("Error: " . $name . " already has an account.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\">");
        return;
    }
    //Check if the Character is already in the database
    $characterDB = $db->fetchRow('SELECT * FROM Characters WHERE Character= :char', array('char' => $name));
    if($db->getRowCount() == 0) {
        printf("Error: According to the database, the character " . $name . " does not exist.<br>Have the potential admin register on EVEOTS first.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\">");
        return;
    }
    $db->insert('Admins', array(
        'username' => $name,
        'password' => $pass,
        'characterID' => $characterDB['CharacterID'],
        'securityLevel' => $security
    ));
    $timestamp = gmdate('d.m.Y H:i');
    $entry = $name . " was given an administrator account (SL " . $security . ") by " . $_SESSION['EVEOTSusername'] . '.';
    AddLogEntry($db, $timestamp, $entry);
    printf("Administrator added.<br>");
    printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\">");
}