<?php

/*
 * 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ==========
 * */

function SaveMember($nickname, $usergroup, $inputID, $inputVCode, $characterID, $blue) {
    $c = new Config;
	try {
		$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$c->tsname.":".$c->tspass."@".$c->tshost.":".$c->tsport."/?server_port=".$c->tscport);
	} catch (TeamSpeak3_Exception $e) {
		die("An error occured: ".$e->getMessage()." [F".__LINE__."]");
	}
	try {
		$tsClient = $ts3_VirtualServer->clientGetByName($nickname); // $tsClient reduces strain on the server
		$tsDatabaseID = $tsClient->client_database_id;
		$tsUniqueID = $tsClient->client_unique_identifier;
		if ($c->verbose == true) {
			echo "<strong>Debug:</strong> Database ID: ".$tsDatabaseID."<br /><strong>Debug:</strong> Unique ID: ".$tsUniqueID."<br />";
		}
	} catch (TeamSpeak3_Exception $e) {
		die("Error: Could not find you on the server, your nickname should be exactly \"$nickname\" (Error: ".$e->getMessage()." [F".__LINE__."])");
	}
	try {
		$ts3_VirtualServer->clientGetByName($nickname)->addServerGroup($usergroup);
	} catch (TeamSpeak3_Exception $e) {
		die("Error: Could not find you on the server, your nickname should be exactly '".$nickname."'. Either that or you already have permissions. (Error: ".$e->getMessage()." [F".__LINE__."])");
	}
	// ATTEMPT TO STORE DETAILS IN DATABASE - IF FAIL THEN REMOVE ACCESS AND REWIND
	try {
		$conINSERT = mysql_connect($c->db_host,$c->db_user,$c->db_pass);
			if (!$conINSERT) {
				$tsClient->remServerGroup($usergroup);
				die("Could not connect: " . mysql_error()." [F".__LINE__."]");
			}
		$db_selectINSERT = mysql_select_db($c->db_name, $conINSERT);
			if (!$db_selectINSERT) {
				$tsClient->remServerGroup($usergroup);
				die("Could not select database: " . mysql_error()." [F".__LINE__."]");
			}
		//destroy any SQL injections that got through our initial checks and "somehow" got through API
		$inputID = mysql_real_escape_string($inputID);
		$inputVCode = mysql_real_escape_string($inputVCode);
		$tsUniqueID = mysql_real_escape_string($tsUniqueID);
		$tsName = mysql_real_escape_string($nickname);
		
		mysql_query("INSERT INTO users (api_kID,api_VCode,characterID,blue,tsDatabaseID,tsUniqueID,tsName) VALUES ('$inputID','$inputVCode','$characterID','$blue','$tsDatabaseID','$tsUniqueID','$tsName')");
		mysql_close($conINSERT);
	} catch (SQL_Exception $e) {
		$tsClient->remServerGroup($usergroup);
		die("Error: Failed to INSERT new member. (Error: ".$e->getMessage()." [F".__LINE__."])");
	}
	echo "Access granted. You should now have permissions on Teamspeak 3.";
}


