<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function WhiteListDelete($db, $id, $type) {
    if($type == 'alliance') {
        //Page refreshed?  ID Already delted?
        $query = $db->fetchRow('SELECT * From Alliances WHERE id= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the alliance in the database, already deleted?<br><br>");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        $alliance = $db->fetchColumn('SELECT AllianceID FROM Alliances WHERE id= :i', array('i' => $id));
        printf("<strong>Remove alliance...</strong><br>Alliance: " . $alliance['Alliance'] . "<br><br>");
        $db->delete('Blues', array('EntityID' => $alliance, 'EntityType' => 1));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $alliance . ' was removed from the alliance white list.';
        AddLogEntry($db, $timestamp, $entry);
        printf("Alliance removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else if ($type == 'corp') {
        $query = $db->fetchRow('SELECT * From Corporations WHERE id= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the corporation in the database, already deleted?<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        $corp = $db->fetchColumn('SELECT CorporationID FROM Corporations WHERE id= :i', array('i' => $id));
        printf("<strong>Removing corporation...</strong><br>Corporation: " . $corp . "<br><br>");
        $db->delete('Blues', array('EntityID' => $corp, 'EntityType' => 2));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $corp . " was removed from the corporation white list.";
        AddLogEntry($db, $timestamp, $entry);
        printf("Corporation removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else if ($type == 'char') {
        $query = $db->fetchRow('SELECT * FROM Characters WHERE id= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the corporation in the database, already deleted?<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        $char = $db->fetchColumn('SELECT CharacterID FROM Characters WHERE id= :i', array('i' => $id));
        printf("<strong>Removing character...</strong><br>Character: " . $char . "<br><br>");
        $db->delete('Blues', array('EntityID' => $char, 'EntityType' => 3));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $char . " was removed from the character white list.";
        AddLogEntry($db, $timestamp, $entry);
        printf("Character removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else {
        printf("Error: Type is not defined.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    }
}