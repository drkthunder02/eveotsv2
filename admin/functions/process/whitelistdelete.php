<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function WhiteListDelete($db, $id, $type) {
    if($type == 1) {
        //Page refreshed?  ID Already delted?
        $alliance = $db->fetchRow('SELECT * FROM Alliances WHERE AllianceID= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the alliance in the database, already deleted?<br><br>");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        printf("<strong>Remove alliance...</strong><br>Alliance: " . $alliance['Alliance'] . "<br><br>");
        $db->delete('Blues', array('EntityID' => $alliance['AllianceID'], 'EntityType' => 1));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $alliance['Alliance'] . ' was removed from the alliance white list.';
        AddLogEntry($db, $timestamp, $entry);
        printf("Alliance removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else if ($type == 2) {
        $corp = $db->fetchRow('SELECT * From Corporations WHERE CorporationID= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the corporation in the database, already deleted?<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        printf("<strong>Removing corporation...</strong><br>Corporation: " . $corp['Corporation'] . "<br><br>");
        $db->delete('Blues', array('EntityID' => $corp['CorporationID'], 'EntityType' => 2));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $corp['Corporation'] . " was removed from the corporation white list.";
        AddLogEntry($db, $timestamp, $entry);
        printf("Corporation removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else if ($type == 3) {
        $char = $db->fetchRow('SELECT * FROM Characters WHERE CharacterID= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the corporation in the database, already deleted?<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            return;
        }
        printf("<strong>Removing character...</strong><br>Character: " . $char['Character'] . "<br><br>");
        $db->delete('Blues', array('EntityID' => $char['CharacterID'], 'EntityType' => 3));
        $timestamp = gmdate('d.m.Y H:i');
        $entry = $char['Character'] . " was removed from the character white list.";
        AddLogEntry($db, $timestamp, $entry);
        printf("Character removed.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    } else {
        printf("Error: Type is not defined.<br><br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
    }
}