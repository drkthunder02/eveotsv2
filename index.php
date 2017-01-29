<?php
/*
========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK
*/
// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
// Required files
require_once __DIR__.'/functions/registry.php';
//Start the session
$session = new \Custom\Session\Sessions();
//Activate the configuration class
$config = new \EVEOTS\Config\Config();
$version = new \EVEOTS\Version\Version();
//Set the default timezone
date_default_timezone_set('Europe/London');



if(isset( $_GET["step"] )) {
    $step = filter_input('GET', 'step');
} else {
    $step = 0;
}
echo "
<html>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Teamspeak 3 Registration</title>
	<link rel='stylesheet' href='style.css' type='text/css'/>
	<script language='javascript' type='text/javascript'>
		function limitText(limitField, limitCount, limitNum) {
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			} else {
				limitCount.value = limitNum - limitField.value.length;
			}
		}
	</script>
	<script type='text/javascript' src='java/jquery.js'></script>
	<script type='text/javascript' src='java/help.js'></script>
</head>
<body>
	<img src=".$c->banner." border='0'><br />
";
switch($step) {
case 0:
case 1:
	?>
		<em>Note: It is recommended you don't connect to Teamspeak until you are asked to.</em><br /><br />
		<form action="index.php?step=2" method="post">
		<table class="details" align="center">
			<tr>
				<td style="text-align: right;">Character Name:</td>
				<td style="text-align: left;"><input name="inputName" size="16"/></td>
			</tr>
			<tr>
				<td style="text-align: right;">Key ID:</td>
				<td><input name="inputID" size="16" /></td>
			</tr>
			<tr>
				<td style="text-align: right;">Verification Code:</td>
				<td><textarea name="inputVCode" rows="3" cols="30" onKeyDown="limitText(this.form.inputAPI,this.form.countdown,65);" onKeyUp="limitText(this.form.inputAPI,this.form.countdown,65);"></textarea></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;"><input name="submit" type="submit" value="Submit" /></td>
			</tr>
		</table>
		Need an EVEOTS API key? Make one <a href="https://support.eveonline.com/api/Key/Create" target="_blank">here</a>. <a onmouseover="popup('<font color=&quot;#FFA600&quot;>Name:</font> EVEOTS<br /><font color=&quot;#FFA600&quot;>Character:</font> All<br /><font color=&quot;#FFA600&quot;>Type:</font> Character<br /><img src=&quot;images/tick.png&quot; border=&quot;0&quot;> No Expiry<br />', '100');"><img src="images/help.png" border="0"></a><br />
		Already have an EVEOTS key? Get it <a href="https://support.eveonline.com/api/Key/Indexhttps://support.eveonline.com/api/Key/Index" target="_blank">here</a>.
		</form>
	<?php
break;
case 2:
	if ($c->verbose == true) {
		echo "<strong>Debug:</strong> Running: Teamspeak 3 PHP Framework version: ".TeamSpeak3::LIB_VERSION."<br /><br />";
	}
	// make sure API is up
	try {
		$testAPI = $pheal->eveScope->CharacterInfo(array("characterID" => $c->TESTID));
		if ($testAPI->characterName == $c->TESTname) {
			echo "API Connection was established.<br /><br />";
		} else {
			echo "API Connection could not be established.<br /> Please try again later, make sure the CCP API server is online first.<br /> If the <u>API</u> server is online and the problem persists please contact your administrator, they likely haven't configured TESTID and TESTname in the config properly.";
			break;
		}
	} catch (PhealException $E) {
		echo "An error occured: ".$E->getMessage()." <strong>(CCP API server <em>may</em> be down)</strong> [".__LINE__."]";
		break;
	}
	// make sure variable are set
	if (!isset($_POST["inputName"]) || !isset($_POST["inputID"]) || !isset($_POST["inputVCode"])) {
		echo "Skipping steps? <a href='index.php'>Go back 3 spaces, do not pass \"Go!\", do not collect &pound;200.</a>";
		break;
	}
	// make sure the form has content
	if ($_POST["inputName"] == "" || $_POST["inputID"] == "" || $_POST["inputVCode"] == "") {
		echo "Error: You must fill in all of the form.";
		break;
	}
	// store the forms details and strip any spaces from either side
	$inputName = trim($_POST["inputName"]);
	$inputID = trim($_POST["inputID"]);
	$inputVCode = trim($_POST["inputVCode"]);
	// make sure there are no spaces in the API Key	
	$spacebar = " ";
	$spacecheckAPI = strpos($inputVCode, $spacebar);
	if ($spacecheckAPI !== false) {
		echo "Error: Your Verification Code has a space in it. Check your vCode is correct before submitting and be careful when copy and pasting! [".__LINE__."]";
		break;
	}
	// make sure there are no spaces in the User ID
	$spacecheckUID = strpos($inputID, $spacebar);
	if ($spacecheckUID !== false) {
		echo "Error: Your Key ID has a space in it. Check your Key ID is correct before submitting and be careful when copy and pasting! [".__LINE__."]";
		break;
	}
	// make sure the form still has content after removing spaces
	if ($inputName == "" || $inputID == "" || $inputVCode == "") {
		echo "Error: You must fill in all of the form.";
		break;
	}
	// initial SQL security checks
	$me = "Character Name";
	sqlCheckNames($inputName,$me);
	$me = "Key ID";
	sqlCheck($inputID,$me);
	$me = "Verification Code";
	sqlCheck($inputVCode,$me);
	
	// create a new Pheal that holds API ready
	$phealapi = new Pheal($inputID,$inputVCode);
	
	// make sure API is for the account and has no expiry
	try {
		$apiAccount = $phealapi->accountScope->APIKeyInfo();
		$apiAccountType = $apiAccount->key->type;
		$apiAccountExpires = $apiAccount->key->expires;
	} catch (PhealAPIException $E) {
		echo "Error: ".$E->getCode()." ".$E->getMessage()." Most likely cause is that your Key ID doesn't match the Verification Code used. [".__LINE__."]";
		break;
	} catch (PhealException $E) {
		echo "Error: Couldn't get API key details from CCP. (Error: ".$e->getMessage().") [".__LINE__."]";
		break;
	}

	if ($apiAccountType !== "Account") {
		echo "Error: Your API must be an account API (<strong>Character:</strong> All), not a character API. Please update your API key.";
		break;
	}
	if ($apiAccountExpires !== "") {
		echo "Error: Your key cannot have an expiry date. Please update your API key.";
		break;
	}
	
	if ($c->verbose == true) {
	echo "Checking...<br /> Character: $inputName<br /><strong>Debug:</strong> Key ID: $inputID<br /><strong>Debug:</strong> vCode: $inputVCode<br /><br />";
	} else {
		echo "Checking...<br /> Character: $inputName<br /><br />";
	}
	// connect to API and get the characterID of who they are claiming to be
	try {
		$APIcharacterID = $pheal->eveScope->CharacterID(array("names" => $inputName));
		foreach($APIcharacterID->characters  as $character) {
			$characterID = $character->characterID;
		}
		if ($characterID == 0) {
			echo "Error: According to the CCP API server, the character \"".$inputName."\" does not exist.";
			break;
		} else {
			if ($c->verbose == true) {
				echo "<strong>Debug:</strong> Character ID: ".$characterID."<br /><br />";
			}
		}
	} catch (PhealException $e) {
		echo "An error occured: Make sure you have entered your character name correctly. (Error: ".$e->getMessage().") [".__LINE__."]";
		break;
	}
	// connect using the provided API details
	try {
		$APIcharacters = $phealapi->accountScope->Characters();
	} catch (PhealException $e) {
		echo "An error occured: API server couldn't retrieve your account or the API wasn't correct, check for spaces after your entered API. (Error: ".$e->getMessage().") [".__LINE__."]";
		break;
	}
	// scan through the characters on this account 
	try {
		if ($c->verbose == true) {
			echo "<strong>Debug:</strong> Character List:<br />";
		}
		$characterCounter = 0;
		$accountCharacterID1 = 0;
		$accountCharacterID2 = NULL;
		$accountCharacterID3 = NULL;
		$verified = NULL;
		
		foreach($APIcharacters->characters as $char) {
			if ($c->verbose == true) {
				echo "<strong>Debug:</strong> ".$char->name." [".$char->characterID."]<br />";
			}
			// record all characterIDs for duplicate checks later
			$characterCounter = $characterCounter + 1;
			if ($characterCounter == 1) {
				$accountCharacterID1 = $char->characterID;
			} else if ($characterCounter == 2) {
				$accountCharacterID2 = $char->characterID;
			} else if ($characterCounter == 3) {
				$accountCharacterID3 = $char->characterID;
			} else {
				echo "Something went wrong on line ".__LINE__.", apparently I can't count.<br />";
			}
			
			// if one of the characterIDs on this account match then verify
			if ($char->characterID == $characterID) {
				$character = $char->name;
				$verified = true;
			}
		}
		if ($c->verbose == true) {
			echo "<br />";
		}
	} catch (PhealException $e) {
		echo "An error occured: ".$e->getMessage()." [".__LINE__."]";
		break;
	}
	
	// process the verified (or not) account
	if ($verified == true) {
		// Ok, we are dealing with the owner of the account, lets get this characters corp/alliance
		if ($c->verbose == true) {
			echo "<strong>Debug:</strong> API verified.<br /><br />";
		}
		try {
			$fetch = $pheal->eveScope->CharacterInfo(array('characterID' => $characterID));
			$fetchCorporation = $fetch->corporation;
			$fetchCorporationID = $fetch->corporationID;
			$fetchAlliance = $fetch->alliance;
			$fetchAllianceID = $fetch->allianceID;
		} catch (PhealException $e) {
			echo "An error occured: ".$e->getMessage()." [".__LINE__."]";
			break;
		}
		// CHECK IF THIS CHARACTERS ALLIANCE/CORP IS ON ANY WHITELIST
		SQLconnect("open");
			$queryAlliance = mysql_query("SELECT * FROM alliances WHERE alliance=\"$fetchAlliance\";");
			$resultAlliance = mysql_num_rows($queryAlliance);
			$queryCorp = mysql_query("SELECT * FROM corporations WHERE corp=\"$fetchCorporation\";");
			$resultCorp = mysql_num_rows($queryCorp);
		SQLconnect("close");
		if ($resultAlliance == 0) {
			// CHECK IF THIS CHARACTERS CORP IS ON THE WHITELIST
			if ($resultCorp == 0) {
				// CHARACTER IS NOT ON ANY WHITELIST
				echo "You are not allowed to register on this server.<br />";
			} else {
				// CHARACTER IS ON OUR CORP WHITELIST
				echo "You are on our corp whitelist<br />";
				if ($fetchCorporation == $c->ourname) {
					// USER IS IN OUR CORP - SET USER GROUP
					$usergroup = $c->group;
					$blue = "No";
				} else {
					// USER IS NOT IN OUR CORP - SET USER GROUP
					$usergroup = $c->bluegroup;
					$blue = "Yes";
				}
				// CONNECT TO PHEAL AND GET CORP TICKER
				if ($c->verbose == true) {
					echo "<strong>Debug:</strong> Getting your corp ticker... ";
				}
				try {
					$corp = $pheal->corpScope->CorporationSheet(array('corporationID' => $fetchCorporationID));
					$corpTicker = $corp->ticker;
				} catch (PhealException $E) {
					echo "An error occured: ".$E->getMessage()." [".__LINE__."]";
					break;
				}
				if ($c->verbose == true) {
					echo $corpTicker."<br />";
				}
				// SET NICKNAME
				if ($c->spacer !== "") {
					$nickname = $corpTicker." ".$c->spacer." ".$character;
				} else {
					$nickname = $corpTicker." ".$character;
				}
				$nickname = substr($nickname, 0, 30); // Teamspeak 3 only allows nicknames of up to 30 characters
				echo "Please connect to Teamspeak 3 <a href=\"ts3server://".$c->tshost."?port=".$c->tscport."&nickname=".rawurlencode($nickname)."&addbookmark=".$c->ourname." Teamspeak&password=".rawurlencode($c->tspassword)."\">automatically</a> or using the following details:<br /> Address: ".$c->tshost.":".$c->tscport."<br /> Nickname: \"".$nickname."\"<br /><br />Once connected, click register.";
				echo "
				<form method='post' action='index.php?step=3'>
					<input type='hidden' name='blue' value=\"".$blue."\" />
					<input type='hidden' name='characterID' value=\"".$characterID."\" />
					<input type='hidden' name='inputVCode' value=\"".$inputVCode."\" />
					<input type='hidden' name='inputID' value=\"".$inputID."\" />
					<input type='hidden' name='nickname' value=\"".$nickname."\" />
					<input type='hidden' name='usergroup' value=\"".$usergroup."\" />
					<input type='hidden' name='accountCharacterID1' value=\"".$accountCharacterID1."\" />
					<input type='hidden' name='accountCharacterID2' value=\"".$accountCharacterID2."\" />
					<input type='hidden' name='accountCharacterID3' value=\"".$accountCharacterID3."\" />
					<input type='submit' value='Register' />
				</form>
				";
			}
		} else {
			// CHARACTER IS ON OUR ALLIANCE WHITELIST
			echo "You are on our alliance whitelist<br /><br />";
			if ($fetchAlliance == $c->ourname) {
				// USER IS IN OUR ALLIANCE - SET USER GROUP
				$usergroup = $c->group;
				$alliancemate = true;
				$blue = "No";
			} else {
				// USER IS NOT IN OUR ALLIANCE - SET USER GROUP
				$usergroup = $c->bluegroup;
				$alliancemate = false;
				$blue = "Yes";
			}
			// CONNECT TO PHEAL AND GET CORP TICKER
			if ($c->verbose == true) {
				echo "<strong>Debug:</strong> Getting your corp ticker... ";
			}
			try {
				$corpSheet = $pheal->corpScope->CorporationSheet(array('corporationID' => $fetchCorporationID));
				$corpTicker = $corpSheet->ticker;
			} catch (PhealException $E) {
				echo "An error occured: ".$E->getMessage()." [".__LINE__."]";
				break;
			}
			if ($c->verbose == true) {
				echo $corpTicker."<br />";
			}
			// CONNECT TO PHEAL AND GET ALLIANCE TICKER
			if ($c->verbose == true) {
				echo "<strong>Debug:</strong> Getting your alliance ticker... ";
			}
			try {
				$allianceList = $pheal->eveScope->AllianceList();
				foreach($allianceList->alliances as $a) {
					// SKIP IF allianceID DOESN'T MATCH THE ONE WE ARE AFTER
					if($a->allianceID == $fetchAllianceID) {
						$allianceTicker = $a->shortName;
					} else {
						continue;						
					}
				}
			} catch (PhealException $E) {
				echo "An error occured: ".$E->getMessage()." <strong>(Demanding resource, may be worth trying again)</strong> [".__LINE__."]";
				break;
			}
			if ($c->verbose == true) {
				echo $allianceTicker."<br />";
			}
			// SET NICKNAME
			if ($alliancemate == true) {
				if ($c->spacer !== "") {
					$nickname = $corpTicker." ".$c->spacer." ".$character;
				} else {
					$nickname = $corpTicker." ".$character;
				} 
			} else {
				if ($c->spacer !== "") {
					$nickname = $allianceTicker." ".$c->spacer." ".$corpTicker." ".$c->spacer." ".$character;
				} else {
					$nickname = $allianceTicker." ".$corpTicker." ".$character;
				}
			}
			$nickname = substr($nickname, 0, 30); // Teamspeak 3 only allows nicknames of up to 30 characters
			echo "Please connect to Teamspeak 3 <a href=\"ts3server://".$c->tshost."?port=".$c->tscport."&nickname=".rawurlencode($nickname)."&addbookmark=".$c->ourname." Teamspeak&password=".rawurlencode($c->tspassword)."\">automatically</a> or using the following details:<br /> Address: ".$c->tshost.":".$c->tscport."<br /> Nickname: \"".$nickname."\"<br /><br />Once connected, click register.";
			echo "
			<form method='post' action='index.php?step=3'>
				<input type='hidden' name='blue' value=\"".$blue."\" />
				<input type='hidden' name='characterID' value=\"".$characterID."\" />
				<input type='hidden' name='inputVCode' value=\"".$inputVCode."\" />
				<input type='hidden' name='inputID' value=\"".$inputID."\" />
				<input type='hidden' name='nickname' value=\"".$nickname."\" />
				<input type='hidden' name='usergroup' value=\"".$usergroup."\" />
				<input type='hidden' name='accountCharacterID1' value=\"".$accountCharacterID1."\" />
				<input type='hidden' name='accountCharacterID2' value=\"".$accountCharacterID2."\" />
				<input type='hidden' name='accountCharacterID3' value=\"".$accountCharacterID3."\" />
				<input type='submit' value='Register' />
			</form>
			";
		}
	} else {
		// COULD NOT VERIFY ACCOUNT HOLDER
		echo "Error: API does not match the character you entered's account. (Denied.)<br />";
	}
break;
case 3:
	if (!isset($_POST["blue"]) || !isset($_POST["characterID"]) || !isset($_POST["inputVCode"]) || !isset($_POST["inputID"]) || !isset($_POST["nickname"]) || !isset($_POST["usergroup"])) {
		echo "Skipping steps? <a href='index.php'>Go back 3 spaces, do not pass \"Go!\", do not collect &pound;200.</a>";
		break;
	}
	$blue = $_POST["blue"];
	$characterID = $_POST["characterID"];
	$inputVCode = $_POST["inputVCode"];
	$inputID = $_POST["inputID"];
	$nickname = $_POST["nickname"];
	$usergroup = $_POST["usergroup"];
	$accountCharacterID1 = $_POST["accountCharacterID1"];
	$accountCharacterID2 = $_POST["accountCharacterID2"];
	$accountCharacterID3 = $_POST["accountCharacterID3"];
		
	if ($blue == "" || $characterID == "" || $inputVCode == "" || $inputID == "" || $nickname == "" || $usergroup == "") {
		echo "Skipping steps? <a href='index.php'>Go back 3 spaces, do not pass \"Go!\", do not collect &pound;200.</a>";
	} else {
		// TRY TO CONNECT - GATHER DETAILS - GRANT PERMISSIONS - STORE DETAILS
		echo "Attempting to grant access to: $nickname...<br /><br />";
		removeDuplicates($accountCharacterID1, $accountCharacterID2, $accountCharacterID3, $inputVCode, $inputID);
		saveMember("$nickname", $usergroup, $inputID, $inputVCode, $characterID, $blue);
	}
break;
}
$link = magicLink("https://gate.eveonline.com/Profile/MJ%20Maverick","935338328","MJ Maverick");
echo "
<div class='footer'>
	<br />
	<br />
	<span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by ".$link."<br />
	Powered by the TS3 PHP Framework & Pheal<br /></span>
	<span style='font-size: 10px;'>EVEOTS $v->release</span>
</div>
</body>
</html>
";
?>