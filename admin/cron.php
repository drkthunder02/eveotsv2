<?php
/*
========== * EVE ONLINE TEAMSPEAK BY MJ MAVERICK * ==========
*/
// Required files
require_once("../pheal/Pheal.php");
require_once("../config.php");
require_once("../functions.php");
// Pheal stuff
spl_autoload_register("Pheal::classload");
PhealConfig::getInstance()->http_keepalive = true;
$pheal = new Pheal(NULL,NULL);
// Activate config
$c = new Config;

//--------------------------------------------------------------------------------------------------------

// make sure API is up
try {
	$testAPI = $pheal->eveScope->CharacterInfo(array("characterID" => $c->TESTID));
	if ($testAPI->characterName == $c->TESTname) {
		format("API Connection was established.\n\n", "API Connection was established.<br /><br />");
	} else {
		format("API Connection could not be established.\n Please contact ".$c->admin.", they likely haven't configured TESTID and TESTname in the config properly.", "API Connection could not be established.<br /> Please contact ".$c->admin.", they likely haven't configured TESTID and TESTname in the config properly.");
		die();
	}
} catch (PhealException $E) {
	format("API Connection could not be established.\n An error occured probably due to the API server being down or taking too long to respond.\n Error: ".$E->getMessage()." [C".__LINE__."]","API Connection could not be established.<br /> An error occured probably due to the API server being down or taking too long to respond.<br /> Error: ".$E->getMessage()." [C".__LINE__."]");
	die();
}

/* ----------------- ALLIANCES ----------------- */
try {
	$allianceList = $pheal->eveScope->AllianceList();
} catch (PhealException $E) {
	echo "An error occured: ".$E->getMessage();
	die();
}
format("Alliances:\n\n","<strong>Alliances</strong><br /><br />");
SQLconnect(open);
$query = mysql_query("SELECT alliance FROM alliances;");
while ($row = mysql_fetch_array($query)) {
	$alliance = "$row[alliance]";
	$allianceMembers = 0;
	// Get this alliances memberCount
	foreach($allianceList->alliances as $a) {
		// skip if alliance doesn't match
		if(strtolower($a->name) == strtolower($alliance)) {
			// store AllianceList details
			$allianceMembers = $a->memberCount;
			break;
		}
	}
	mysql_query("UPDATE alliances SET memberCount=\"$allianceMembers\" WHERE alliance=\"$alliance\";");
	format("Alliance: ".$alliance."\n Member count updated to: ".number_format($allianceMembers)."\n \n","Alliance: ".$alliance."<br /> Member count updated to: ".number_format($allianceMembers)."<br /><br />");
}
SQLconnect(close);

/* ------------------- CORPS ------------------- */
format("Corporations:\n\n","<strong>Corporations</strong><br /><br />");
SQLconnect(open);
$query = mysql_query("SELECT * FROM corporations;");
while ($row = mysql_fetch_array($query)) {
	$corpID = "$row[corpID]";
	$corp = "$row[corp]";
	$corpMembers = 0;
	// Get this corps memberCount
	try {
		$fetch = $pheal->corpScope->CorporationSheet(array('corporationID' => $corpID));
		$corpMembers = $fetch->memberCount;
		$corpAlliance = $fetch->allianceName;
	} catch (PhealException $e) {
		die("An error occured: ".$e->getMessage()." [AP".__LINE__."]");
	}
	mysql_query("UPDATE corporations SET memberCount=\"$corpMembers\" WHERE corpID=\"$corpID\";");
	mysql_query("UPDATE corporations SET corpAlliance=\"$corpAlliance\" WHERE corpID=\"$corpID\";");
	format("Corporation: ".$corp."\n Member count updated to: ".number_format($corpMembers)."\n \n","Corporation: ".$corp."<br /> Member count updated to: ".number_format($corpMembers)."<br /><br />");
}
SQLconnect(close);

format("Job Completed.","Job Completed.");
?>
