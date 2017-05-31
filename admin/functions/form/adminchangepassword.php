<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function AdminChangePassword($newPassword, $newPConfirm, \Simplon\Mysql\Mysql $db) {
    if ($_POST["newPassword"] == "" || $_POST["newPConfirm"] == "") {
        echo "Error: Please fill both fields. Type your desired password then confirm it by typing it again in the \"Confirm\" field.<br /><br />";
        echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
        return;
    } else if ($_POST["newPassword"] != $_POST["newPConfirm"]) {
        echo "Error: The passwords do not match.<br /><br />";
        echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
        return;
    } else if (preg_match("/^[a-zA-Z0-9]+$/", $_POST["newPassword"]) == 0) {
        // Make sure password is only a-z A-Z 0-9
        echo "Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />";
        echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
        return;
    } else {
        printf("Changing password...<br>");
        $newPassword = md5($newPassword);
        $sid = $_SESSION["EVEOTSid"];
        $db->update('admins', array('id' => $sid), array('password' => $newPassword));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $_SESSION["EVEOTSusername"] . " change their password.";
        AddLogEntry($db, $timestamp, $entry);
        printf("Your password has been changed.<br>");
    }
}


            