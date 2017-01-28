<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

function DeleteSingleUser(\Simplon\Mysql\Mysql $db, $id, \EVEOTS\Config\Config $config) {
    $session = new \Custom\Session\Sessions();
    
    $ts = $config->GetTSConfig();
    $tsServerQuery = $config->GetTSServerQuery();
    
    $client = $db->fetchRow('SELECT * FROM Users WHERE id= :id', array('id' => $id));
    if($client == NULL) {
        printf("This client no longer seems to exist.");
        return;
    }
    $tsDatabaseID = $client['TSDatabaseID'];
    $tsUniqueID = $client['TSUniqueID'];
    $tsName = $client['TSName'];
    
    printf("Attempting to remove \"".$tsName."\" from Teamspeak.<br /><br />");
    
    try {
        $ts3_VirtualServer = TeamSpeak3::factory($tsServerQuery);
    } catch (TeamSpeak3_Exception $e) {
        printf("Error: ".$e->getMessage()." [A".__LINE__."]");
        return;
    }
    // check if client is online and kick if they are
    try {
        $online = $ts3_VirtualServer->clientGetIdsByUid($tsUniqueID);
        printf("Client online. Attempting kick... ");
        try {
            $ts3_VirtualServer->clientGetByUid($tsUniqueID)->Kick(TeamSpeak3::KICK_SERVER, "Teamspeak access revoked by ".$_SESSION["EVEOTSusername"].".");
            printf("Kicked.<br />Deleting client from Teamspeak... ");
        } catch (TeamSpeak3_Exception $e) {
            printf("FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]");
            return;
        }
    } catch(Teamspeak3_Exception $e) {
            printf("Client offline.<br />Deleting from Teamspeak... ");
    }
    // delete client from Teamspeak
    try {
        $ts3_VirtualServer->clientdeleteDb($tsDatabaseID);
        printf("Done.<br />");
        // delete client from database
        try {
            printf("Deleting client from user database... ");
            $db->delete('Users', array(
                'TSDatabaseID' => $tsDatabaseID,
            ));            
            printf("Done.<br /><br />All operations completed successfully, \"".$tsName."\" has been removed.<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />");
            //Add Log Entry
            $timestamp = gmdate('d.m.Y H:i');
            $entry = $_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")";
            AddLogEntry($db, $timestamp, $entry);
        } catch (TeamSpeak3_Exception $e) {
            printf("FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />");
        }
    } catch (TeamSpeak3_Exception $e) {
        if ($e->getMessage() == "invalid clientID") {
            printf("Client did not exist on Teamspeak. (".$tsDatabaseID.")<br />");
            try {
                printf("Deleting client from user database... ");
                $db->delete('Users', array(
                    'TSDatabaseID' => $tsDatabaseID,
                ));
                    
                printf("Done.<br /><br />All operations completed successfully, \"".$tsName."\" has been removed.<br /><br />");
                printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />");
                //Add log entry
                $timestamp = gmdate('d.m.Y H:i');
                $entry = $_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")";
                AddLogEntry($db, $timestamp, $entry);
            } catch (TeamSpeak3_Exception $e) {
                printf("FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />");
            }
        } else {
            printf("FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]<br />");
        }
    }
}