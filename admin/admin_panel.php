<html>
<head>
	<title>EVEOTS Admin Panel</title>
	<link rel="stylesheet" href="admin.css" type="text/css"/>
	<script type='text/javascript' src='../java/jquery.js'></script>
	<script type="text/javascript" src="../java/progress.js"></script>
	<script type="text/javascript" src="../java/checkbox.js"></script>
</head>
<body>
<table height="100%" width="1000px" cellspacing="2" border="0" align="center" class="maintable">
	<tr>
		<td width="10px" class="left" rowspan="3"></td>
		<td height="105px" colspan="2" align="center"><img src="images/banner.png" border="0"></td>
		<td width="10px" class="right" rowspan="3"></td>
	</tr>
	<tr valign="top">
		<td width="200px">
			<?php echo "Logged in as:<br /><em>".$_SESSION["EVEOTSusername"]."</em><br />"; ?>
			<a href="logout.php">Logout</a><br /><br />
			<a href="admin_panel.php?menu=main">Main</a><br />
			<a href="admin_panel.php?menu=change_password">Change Password</a><br />
			<a href="admin_panel.php?menu=logs">Logs</a><br /><br />
			<a href="admin_panel.php?menu=admins_audit">Audit Administrators</a><br />
			<a href="admin_panel.php?menu=members_audit">Audit Members</a><br />
			<a href="admin_panel.php?menu=members_discrepancies">>> Discrepancies</a><br /><br />
			<a href="admin_panel.php?menu=whitelist">Edit Whitelist</a><br />
		</td>
		<td>
			<?php
				SQLconnect("open");
					$queryadmin = mysql_query("SELECT username FROM admins WHERE username=\"admin\";");
					if (mysql_num_rows($queryadmin) > 0) {
						$installAccount = true;
					} else {
						$installAccount = false;
					}
				SQLconnect("close");
				if(isset( $_GET["menu"] )) {
					$menu = $_GET["menu"];
				} else {
					$menu = "main";
				}
				switch ($menu) {
				    case "main":
				    	if ($installAccount == true) {
				    		echo "<br /><div class=\"installAccount\" align=\"center\"><font class=\"installAccountTitle\">Warning: Setup not complete!<br /></font>User \"admin\" is still in the database. This is a massive security risk. Please create yourself a new account, set it as the root admin and delete the default \"admin\" account immediately.<br /> See the readme \"How To Setup A New Root Admin\" for more detailed instructions.</div>";
				    	}
						echo "<br />Welcome to your EVEOTS admin panel, here you can manage admins along with which Corporations and Alliances can access your Teamspeak 3 server.<br /><br />";
						echo "Here are a few things you should know:<br />";
						echo "<ul>";
						echo "<li><strong>Root Administrator -</strong> This person should be the main administrator. This user cannot be deleted or edited by other administrators. This person will be highlighted wherever administrators are listed.<br /><font size=\"2\">To change the root admin from default (recommended) you need to go into the \"admins\" table in the database and get the \"id\" number of the root admin to-be. Then open config.php and set \$adminID as that number.</font><br /><br /></li>";
						echo "<li><strong>Security Level (SL) -</strong> This is the level of control someone has.<br /> 1 = Super admin. Able to edit, delete and create others via the Audit menus.<br /> 2 = Normal admin. Can only make changes to the whitelist. Cannot access the audit menus (recommended).<br /><br /></li>";
						echo "<li><strong>Administrator Lists -</strong> The corporation/alliance listed is LIVE. So keep an eye out for one of your admins leaving your corporation or alliance and still having access.<br /><br /></li>";
						echo "</ul>";
						echo "<strong>Administrators:</strong><br />";
						// Connect to server and select database.
						SQLconnect("open");
							// Set query
							$query = mysql_query("SELECT * FROM admins ORDER BY username;");
							// Build admin table
							echo "<table width=\"100%\" border=\"0\">";
							echo "<tr><td width=\"32px\"></td><td>Username</td><td align=\"center\">Corporation</td><td width=\"100px\">Security Level</td></tr>";
							// Populate admin table
							while ($row = mysql_fetch_array($query)) {
								$id = "$row[id]";
								$characterID = "$row[characterID]";
								$username = "$row[username]";
								$securityLevel = "$row[securityLevel]";
								// Get the characters current corporation
								if ($characterID !== "") {
									try {
										$fetch = $pheal->eveScope->CharacterInfo(array('characterID' => $characterID));
										$fetchCorporation = $fetch->corporation;
										$fetchAlliance = $fetch->alliance;
									} catch (PhealException $e) {
										die("An error occured: ".$e->getMessage()." [A".__LINE__."]");
									}
								} else {
									$fetchCorporation = "";
									$fetchAlliance = "";
								}
								if ($id == $c->adminID) {
									echo "<tr class=\"listRootAdmin\">";
								} else {
									echo "<tr>";
								}
								echo "<td width=\"32px\">";
									if ($characterID !== "") {
										echo "<img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\">";
									} else {
										echo "<img src=\"images/admin.png\" border=\"0\">";
									}
								echo "</td><td>".$username."</td><td align=\"center\">".$fetchCorporation."<br /><font size=\"2\">".$fetchAlliance."</font></td><td align=\"center\">".$securityLevel."</td></tr>";
							}
							echo "</table>";
						SQLconnect("close");
					break;
				    case "change_password":
				    	if (!isset($_POST["newPassword"])) {
				    		echo "Passwords can only contain A-Z, a-z and 0-9.<br /><br />";
					        ?>
							<form action="?menu=change_password" method="post">
							<table>
								<tr>
									<td style="text-align: right;"><font size="2">Password:</font></td>
									<td style="text-align: left;"><input type="password" name="newPassword" size="16" /></td>
									<td style="text-align: right;"><font size="2">Confirm:</font></td>
									<td style="text-align: left;"><input type="password" name="newPConfirm" size="16"/></td>
									<td colspan="6"><input name="submit" type="submit" value="Change" /></td>
								</tr>
							</table>
							</form>
							<?php
						} else {
							if ($_POST["newPassword"] == "" || $_POST["newPConfirm"] == "") {
								echo "Error: Please fill both fields. Type your desired password then confirm it by typing it again in the \"Confirm\" field.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($_POST["newPassword"] != $_POST["newPConfirm"]) {
								echo "Error: The passwords do not match.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if (preg_match("/^[a-zA-Z0-9]+$/", $_POST["newPassword"]) == 0) {
								// Make sure password is only a-z A-Z 0-9
								echo "Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else {
								echo "Changing password...<br />";
								$newPassword = md5($_POST["newPassword"]);
								$sid = $_SESSION["EVEOTSid"];
								// Connect to the database and UPDATE password
								SQLconnect("open");
									$update = mysql_query("UPDATE admins SET password=\"$newPassword\" WHERE id=\"$sid\";");
									// START Log
									$timestamp = gmdate('d.m.Y H:i');
									$timestamp = mysql_real_escape_string($timestamp);
									$log = $_SESSION["EVEOTSusername"]." changed their password.";
									$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
									// END Log
								SQLconnect("close");
								echo "Your password has been changed.<br />";
							}
						}
					break;
					case "logs":
						if ($_SESSION["EVEOTSid"] == $c->adminID) {
							?>		
							<form action="?menu=logs" method="post">
							<table>
								<tr>
									<td>Root administrator option: <input name="clear_logs" type="submit" value="Clear logs" onclick="return confirm('Are you sure you want clear all logs?')" /></td>
								</tr>
							</table>
							</form>
							<?php
						}
						if (isset($_POST["clear_logs"])) {
							echo "Clearing logs...<br />";
							SQLconnect("open");
								$truncate = mysql_query("TRUNCATE logs;");
								if (!$truncate) {
								    die('Invalid query: ' . mysql_error());
								}
							SQLconnect("close");
							echo " Logs cleared.<br /><br />";
						}
						SQLconnect("open");
							// Set query
							$query = mysql_query("SELECT * FROM logs ORDER BY id DESC;");
							$count = mysql_num_rows($query);
							if ($count > 0) {
							$cssClass = "odd";
							// Build logs table
							echo "<table width=\"100%\" cellspacing=\"0\" border=\"0\">";
							echo "<tr><td align=\"center\" width=\"150px\">Time</td><td>Log</td></tr>";
							$cssClass = "odd";
								// Populate logs table
								while ($row = mysql_fetch_array($query)) {
									$timestamp = "$row[time]";
									$log = "$row[log]";
									echo "<tr class=\"".$cssClass."\"><td align=\"center\">".$timestamp."</td><td>".$log."</td></tr>";
									if ($cssClass == "even") {
										$cssClass = "odd";
									} else if ($cssClass == "odd") {
										$cssClass = "even";
									}
								}
								echo "</table>";
							} else {
								echo "No logs found.";
							}
						SQLconnect("close");
					break;
					case "admins_add":
						// Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							// Trim any spaces either side
							$adminCharacterName = trim($_POST["adminCharacterName"]);
							$adminPassword = trim($_POST["adminPassword"]);
							$adminSecurityLevel = trim($_POST["adminSecurityLevel"]);
							// Make sure all fields are filled in
							if ($adminCharacterName == "") {
								echo "Character Name cannot be blank.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($adminPassword == "") {
								echo "Password cannot be blank.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($adminSecurityLevel == "") {
								echo "Security level cannot be blank.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($adminSecurityLevel !== "1" && $adminSecurityLevel !== "2") {
								echo "Security level must be 1 or 2.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Make sure password is only a-z A-Z 0-9
							if(preg_match("/^[a-zA-Z0-9]+$/", $adminPassword) === 0) {
								echo "Error: Passwords can only contain A-Z, a-z and 0-9<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Check if the Character Name is already in use
							SQLconnect("open");
								$query = mysql_query("SELECT username FROM admins WHERE username=\"$adminCharacterName\";");
								$count = mysql_num_rows($query);
							SQLconnect("close");
							if ($count > 0) {
								echo "Error: ".$adminCharacterName." already has an account.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Check if the Character Name is legit and get the Character ID
							try {
								$APIcharacterID = $pheal->eveScope->CharacterID(array("names" => $adminCharacterName));
								foreach($APIcharacterID->characters  as $character) {
									$adminCharacterID = $character->characterID;
								}
							} catch (PhealException $e) {
								die("An error occured: Make sure you have entered the character name correctly. (Error: ".$e->getMessage().") [A".__LINE__."]");
							}
							if ($adminCharacterID == 0) {
								echo "Error: According to the CCP API server, the character \"".$adminCharacterName."\" does not exist.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Using the Character ID, get their name as it appears in-game
							try {
								$fetch = $pheal->eveScope->CharacterInfo(array('characterID' => $adminCharacterID));
								$adminCharacterName = $fetch->characterName;
							} catch (PhealException $e) {
								die("An error occured: ".$e->getMessage()." [A".__LINE__."]");
							}
							echo "<strong>Adding administrator...</strong><br />Character: ".$adminCharacterName."<br />Password: ".$adminPassword."<br />Security Level: ".$adminSecurityLevel."<br />Character ID: ".$adminCharacterID."<br /><br />";
							// Connect to the database and INSERT admin
							SQLconnect("open");
								$adminCharacterName = mysql_real_escape_string($adminCharacterName);
								$adminPassword = md5($adminPassword);
								$query = mysql_query("INSERT INTO admins (username, password, securityLevel, characterID) VALUES (\"$adminCharacterName\", \"$adminPassword\", \"$adminSecurityLevel\", \"$adminCharacterID\");");
								// START Log
								$timestamp = gmdate('d.m.Y H:i');
								$timestamp = mysql_real_escape_string($timestamp);
								$log = $adminCharacterName." was given an administrator account (SL ".$adminSecurityLevel.") by ".$_SESSION["EVEOTSusername"].".";
								$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
								// END Log
							SQLconnect("close");
							echo "Administrator added.<br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
						}
					break;
					case "admins_audit":
				        // Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							// Audit
							echo "<strong>Add administrator:</strong><br /><font size=\"2\">Character Name: Must be their exact in-game name.<br />Password: a-Z & 0-9 only.<br />Security Level: 1 = Super Admin. 2 = Disables Audit menus.</font>";
							?>		
							<form action="?menu=admins_add" method="post">
							<table align="center">
								<tr>
									<td style="text-align: right;"><font size="2">Character Name:</font></td>
									<td style="text-align: left;"><input name="adminCharacterName" size="16"/></td>
									<td style="text-align: right;"><font size="2">Password:</font></td>
									<td style="text-align: left;"><input name="adminPassword" size="16" /></td>
									<td style="text-align: right;"><font size="2">Security Level (1/2):</font></td>
									<td style="text-align: left;"><input name="adminSecurityLevel" size="1"/></td>
								</tr>
								<tr>
									<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Add" /></td>
								</tr>
							</table>
							</form>
							<?php
							// Connect to server and select database.
							SQLconnect("open");
								// Set query
								$query = mysql_query("SELECT * FROM admins ORDER BY username;");
								// Build admin table
								echo "<table width=\"100%\" border=\"0\">";
								echo "<tr><td width=\"32px\"></td><td>Username</td><td align=\"center\">Corporation</td><td width=\"100px\">Security Level</td><td width=\"40px\"></td></tr>";
								// Populate admin table
								while ($row = mysql_fetch_array($query)) {
									$id = "$row[id]";
									$characterID = "$row[characterID]";
									$username = "$row[username]";
									$securityLevel = "$row[securityLevel]";
									// Get the characters current corporation
									if ($characterID !== "") {
										try {
											$fetch = $pheal->eveScope->CharacterInfo(array('characterID' => $characterID));
											$fetchCorporation = $fetch->corporation;
											$fetchAlliance = $fetch->alliance;
										} catch (PhealException $e) {
											die("An error occured: ".$e->getMessage()." [A".__LINE__."]");
										}
									} else {
										$fetchCorporation = "";
										$fetchAlliance = "";
									}

									if ($id == $c->adminID) {
										echo "<tr class=\"listRootAdmin\">";
									} else {
										echo "<tr>";
									}
									echo "<td width=\"32px\">";
										if ($characterID !== "") {
											echo "<img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\">";
										} else {
											echo "<img src=\"images/admin.png\" border=\"0\">";
										}
									echo "</td><td>".$username."</td><td align=\"center\">".$fetchCorporation."<br /><font size=\"2\">".$fetchAlliance."</font></td><td align=\"center\">".$securityLevel."</td>";
									if ($id == $c->adminID && $id == $_SESSION["EVEOTSid"]) {
										echo "<td><a href=\"?menu=admins_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a></td></tr>";
									} else if ($id == $c->adminID) {
										echo "<td></td></tr>";
									} else {
										echo "<td><a href=\"?menu=admins_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a> <a href=\"?menu=admins_delete&id=".$id."\" onclick=\"return confirm('Are you sure you want to delete ".$username."?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td></tr>";
									}
								}
								echo "</table>";
							SQLconnect("close");
						}
					break;
					case "admins_delete":
						// Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							$id = $_GET["id"];
							if ($id == $c->adminID) {
								echo "This is the root admin! You cannot delete this user.";
								break;
							}
							SQLconnect("open");
								$query = mysql_query("SELECT * FROM admins WHERE id=$id;");
								$count = mysql_num_rows($query);
							SQLconnect("close");
							if ($count == 0) {
								echo "Error: Couldn't find the admin in the database, already deleted?<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							SQLconnect("open");
								while ($row = mysql_fetch_array($query)) {
									$username = "$row[username]";
									$securityLevel = "$row[securityLevel]";
								}
								echo "<strong>Deleting administrator...</strong><br />Character: ".$username."<br />Security Level: ".$securityLevel."<br /><br />";
								$queryDelete = mysql_query("DELETE FROM admins WHERE id=$id;");
								// START Log
								$timestamp = gmdate('d.m.Y H:i');
								$timestamp = mysql_real_escape_string($timestamp);
								$log = $username."'s administrator account was deleted by ".$_SESSION["EVEOTSusername"].".";
								$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
								// END Log
							SQLconnect("close");
							echo "Administrator deleted.<br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
						}
					break;
					case "admins_edit":
						$id = $_GET["id"];
						SQLconnect("open");
							// Who are we editing?
							$query = mysql_query("SELECT username FROM admins WHERE id=$id;");
							while ($row = mysql_fetch_array($query)) {
								$username = "$row[username]";
							}
							// Get the editors security level
							$query = mysql_query("SELECT securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
							}
						SQLconnect("close");
						// Is this person allowed to edit admins?
						if ($securityLevel !== "1") {
							echo "You are not authorised to access this area.<br />";
							break;
						// Trying to edit to root admin?
						} else if ($id == $c->adminID && $_SESSION["EVEOTSid"] != $c->adminID) {
							echo "You are not permitted to edit the root admin!<br />";
							break;
						}
						// Are we going to edit the root admin?
						if ($id == $c->adminID) {
							$rootAdminEdit = true;
						} else {
							$rootAdminEdit = false;
						}
						// Edit admin
						echo "<strong>Editing user:</strong> ".$username."<br /><br />";
						// Change password
						echo "<strong>Change password:</strong><br />";
						if (!isset($_POST["newPassword"])) {
							echo "<form action=\"?menu=admins_edit&id=$id\" method=\"post\">";
							?>
							<table>
								<tr>
									<td style="text-align: right;"><font size="2">New Password:</font></td>
									<td style="text-align: left;"><input type="password" name="newPassword" size="16" /></td>
									<td style="text-align: right;"><font size="2">Confirm:</font></td>
									<td style="text-align: left;"><input type="password" name="newPConfirm" size="16"/></td>
									<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
								</tr>
							</table>
							<?php
							echo "</form>";
						} else if (isset($_POST["newPassword"])) {
							// Change the password
							if ($_POST["newPassword"] == "" || $_POST["newPConfirm"] == "") {
								echo "Error: Please fill both password fields. Type the desired password then confirm it by typing it again in the \"Confirm\" field.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($_POST["newPassword"] != $_POST["newPConfirm"]) {
								echo "Error: The new passwords do not match.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if (preg_match("/^[a-zA-Z0-9]+$/", $_POST["newPassword"]) == 0) {
								// Make sure password is only a-z A-Z 0-9
								echo "Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else {
								echo "Changing password...<br />";
								$newPassword = md5($_POST["newPassword"]);
								// Connect to the database and UPDATE password
								SQLconnect("open");
									$update = mysql_query("UPDATE admins SET password=\"$newPassword\" WHERE id=\"$id\";");
									// START Log
									$timestamp = gmdate('d.m.Y H:i');
									$timestamp = mysql_real_escape_string($timestamp);
									$log = $_SESSION["EVEOTSusername"]." changed ".$username."'s password.";
									$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
									// END Log
								SQLconnect("close");
								echo $username."'s password has been changed.<br />";
							}
						}
						// Change security level
						echo "<strong>Change security level:</strong><br />";
						if ($rootAdminEdit == true) {
							echo "The root admins security level cannot be changed.<br />";
						} else if (!isset($_POST["newSL"])) {
							echo "<form action=\"?menu=admins_edit&id=$id\" method=\"post\">";
							?>
							<table>
								<tr>
									<td style="text-align: right;"><font size="2">New Security Level (1/2):</font></td>
									<td style="text-align: left;"><input name="newSL" size="1" /></td>
									<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
								</tr>
							</table>
							<?php
							echo "</form>";
						} else if (isset($_POST["newSL"])) {
							// Change the password
							if ($_POST["newSL"] == "") {
								echo "Error: Security level was blank. Please input 1 or 2.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else if ($_POST["newSL"] !== "1" && $_POST["newSL"] !== "2") {
								echo "Error: Security level must be 1 or 2.<br />";
								echo "<form action=\"?menu=admins_edit&id=$id\" method=\"post\">";
								?>
								<table>
									<tr>
										<td style="text-align: right;"><font size="2">New Security Level (1/2):</font></td>
										<td style="text-align: left;"><input name="newSL" size="1" /></td>
										<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
									</tr>
								</table>
								<?php
								echo "</form>";
							} else {
								echo "Changing security level...<br />";
								// Connect to the database and UPDATE security level
								$newSL = $_POST["newSL"];
								SQLconnect("open");
									$queryCheck = mysql_query("SELECT securityLevel FROM admins WHERE id=\"$id\";");
									while ($row = mysql_fetch_array($queryCheck)) {
										$currentSL = "$row[securityLevel]";
									}
									if ($currentSL == $newSL) {
										echo $username." already has a security level of ".$newSL.", nothing was changed.<br />";
									} else {
										$update = mysql_query("UPDATE admins SET securityLevel=\"$newSL\" WHERE id=\"$id\";");
										// START Log
										$timestamp = gmdate('d.m.Y H:i');
										$timestamp = mysql_real_escape_string($timestamp);
										$log = $_SESSION["EVEOTSusername"]." changed ".$username."'s security level to ".$newSL.".";
										$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
										// END Log
										echo $username."'s security level has been changed.<br />";
									}
								SQLconnect("close");
								echo "<form action=\"?menu=admins_edit&id=$id\" method=\"post\">";
								?>
								<table>
									<tr>
										<td style="text-align: right;"><font size="2">New Security Level (1/2):</font></td>
										<td style="text-align: left;"><input name="newSL" size="1" /></td>
										<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
									</tr>
								</table>
								<?php
								echo "</form>";
							}
						}
					break;
					case "members_audit":
						// Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} elseif (isset($_POST["search"]) && $_POST["search"] !== "") {
							// Search results
							$search = $_POST["search"];
							echo "<em>Note: This is an UNRESTRICTED search. ALL hits will be displayed and on one page.</em><br /><br />";
							echo "<a href=\"?menu=members_audit\">Reset filter</a><br />";
							SQLconnect("open");
								$query = mysql_query("SELECT * FROM users WHERE LOWER(tsName) LIKE LOWER(\"%".$search."%\") ORDER BY tsName ASC;");
								$numRows = mysql_num_rows($query);
								if ($numRows < 1) {
									echo "<br /><strong>NO RESULTS</strong><br />";
								} else {
									echo "<table width=\"100%\" cellspacing=\"2\">";
									echo "<tr> <td></td> <td></td> <td width=\"90px\" align=\"right\">Database ID</td> <td width=\"16px\"></td> </tr>";
									while ($row = mysql_fetch_array($query)) {
										$id = "$row[entryID]";
										$characterID = "$row[characterID]";
										$blue = "$row[blue]";
										$tsDatabaseID = "$row[tsDatabaseID]";
										$tsUniqueID = "$row[tsUniqueID]";
										$tsName = "$row[tsName]";
										
										if ($blue == "Yes") {
											$icon = "images/blue.png";
										} else {
											$icon = "images/ally.png";
										}
										echo "<tr>";
										echo "<td width=\"32px\"><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td>";
										echo "<td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td>";
										echo "<td align=\"right\">".$tsDatabaseID."</td>";
										echo "<td><a href=\"?menu=members_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a> <a href=\"?menu=members_delete&id=".$id."\" onclick=\"return confirm('Confirm removal of &quot;".$tsName."&quot; from Teamspeak?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td>\n";
										echo "</tr>";
									}
									echo "</table>";
								}
							SQLconnect("close");

							echo "<br /><a href=\"?menu=members_audit\">Reset filter</a>";
						} else {
							// Calculate pages
							$listAmount = 50;
							SQLconnect("open");
								$queryRows = mysql_query("SELECT blue FROM users;");
								$userCount = mysql_num_rows($queryRows);
								$queryBlue = mysql_query("SELECT blue FROM users WHERE blue = \"Yes\";");
								$blueCount = mysql_num_rows($queryBlue);
								$userCountMinusBlues = $userCount - $blueCount;
							SQLconnect("close");
							$exactPages = $userCount / $listAmount;
							$maxPages = ceil($exactPages);
							if (isset($_GET["page"])) {
							$page = $_GET["page"];
								if ($page == 1 || $page == NULL || $page == 0) {
									$page = 1;
									$listFrom = 0;
									$nextPage = 2;
								} else {
									$listFrom = $page * $listAmount - $listAmount;
									$nextPage = $page + 1;
									$backPage = $page - 1;
								}
							} else {
								$page = 1;
								$listFrom = 0;
								$nextPage = 2;
							}
							echo $userCount." Registered members.<br />".$userCountMinusBlues." Excluding blues.<br /><br />";
							
							// Search
							echo "<form action=\"?menu=members_audit\" method=\"post\">";
							?>
							<table>
								<tr>
									<td style="text-align: left;"><input name="search" size="20" /></td>
									<td style="text-align: right;"><input name="submit" type="submit" value="Search" /></td>
								</tr>
							</table>
							<?php
							echo "</form>";

							// BACK / NEXT
							if (!isset($backPage)) {
								echo "Back | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />";
							} else if (!isset($nextPage)) {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />";
							} else if ($page >= $maxPages) {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />";
							} else {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />";
							}
							echo "<strong>".$page." / ".$maxPages."</strong><br />";
							SQLconnect("open");
								$query = mysql_query("SELECT * FROM users ORDER BY tsName ASC LIMIT ".$listFrom.",".$listAmount.";");
								echo "<table width=\"100%\" cellspacing=\"2\">";
								echo "<tr> <td></td> <td></td> <td width=\"90px\" align=\"right\">Database ID</td> <td width=\"16px\"></td> </tr>";
								while ($row = mysql_fetch_array($query)) {
									$id = "$row[entryID]";
									$characterID = "$row[characterID]";
									$blue = "$row[blue]";
									$tsDatabaseID = "$row[tsDatabaseID]";
									$tsUniqueID = "$row[tsUniqueID]";
									$tsName = "$row[tsName]";
									
									if ($blue == "Yes") {
										$icon = "images/blue.png";
									} else {
										$icon = "images/ally.png";
									}
									echo "<tr>";
									echo "<td width=\"32px\"><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td>";
									echo "<td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td>";
									echo "<td align=\"right\">".$tsDatabaseID."</td>";
									echo "<td><a href=\"?menu=members_edit&id=".$id."\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a> <a href=\"?menu=members_delete&id=".$id."\" onclick=\"return confirm('Confirm removal of &quot;".$tsName."&quot; from Teamspeak?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td>\n";
									echo "</tr>";
								}
								echo "</table>";
							SQLconnect("close");
							// BACK / NEXT
							if (!isset($backPage)) {
								echo "Back | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />";
							} else if (!isset($nextPage)) {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />";
							} else if ($page >= $maxPages) {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | Next<br />";
							} else {
								echo "<a href=\"?menu=members_audit&page=".$backPage."\">Back</a> | <a href=\"?menu=members_audit&page=".$nextPage."\">Next</a><br />";
							}
						}
					break;
					case "members_delete":
						// Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							if (isset($_GET["discrepancies"])) {
								// Delete discrepancies
								$discrepancy = $_POST["discrepancyDelete"];
								if (empty($discrepancy)) {
									echo "Error: No discrepancies selected to be deleted.<br /><br />";
									echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />";
									break;
								} else {
								    echo "Deleting discrepancies...<br /><br />";
									echo "<table width=\"100%\" border=\"0\"> <tr><td width=\"100\">Deleteing...</td> <td width=\"32\"></td> <td></td> <td width=\"110\">TS Database ID</td> <td></td></tr>";
								    $deleted = 0;
								    foreach ($discrepancy as $discrep) {
										SQLconnect("open");
											$query = mysql_query("SELECT characterID,blue,tsDatabaseID,tsUniqueID,tsName FROM users WHERE entryID = $discrep;");
											while ($row = mysql_fetch_array($query)) {
												$characterID = "$row[characterID]";
												$blue = "$row[blue]";
												$tsDatabaseID = "$row[tsDatabaseID]";
												$tsUniqueID = "$row[tsUniqueID]";
												$tsName = "$row[tsName]";
											}
											//Delete entry
											$queryDelete = "DELETE FROM users WHERE entryID = '".$discrep."';";
											mysql_query($queryDelete);
											$deleted = $deleted + 1;
											// Print
											if ($blue == "Yes") {
												$icon = "images/blue.png";
											} else {
												$icon = "images/ally.png";
											}
											echo "<tr bgcolor=\"#151515\"><td>".$discrep."</td> <td><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td> <td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td> <td align=\"center\">".$tsDatabaseID."</td> <td align=\"center\">Deleted</td></tr>";
										SQLconnect("close");
									}
									echo "</table><br />";
									echo "Discrepancies removed: ".$deleted."<br />";
								}
							} else {
								// Delete single user
								$id = $_GET["id"];
								SQLconnect("open");
									// Gather required details for deleting.
									$query = mysql_query("SELECT tsDatabaseID,tsUniqueID,tsName FROM users WHERE entryID=$id;");
									$clientExists = mysql_num_rows($query);
									if ($clientExists == 0) {
										echo "This client no longer seems to exists.";
										break;
									}
									while ($row = mysql_fetch_array($query)) {
										$entryID = $id;
										$tsDatabaseID = "$row[tsDatabaseID]";
										$tsUniqueID = "$row[tsUniqueID]";
										$tsName = "$row[tsName]";
									}
								SQLconnect("close");
								echo "Attempting to remove \"".$tsName."\" from Teamspeak.<br /><br />";
								// connect to Teamspeak
								try {
									$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$c->tsname.":".$c->tspass."@".$c->tshost.":".$c->tsport."/?server_port=".$c->tscport);
								} catch (TeamSpeak3_Exception $e) {
									echo "Error: ".$e->getMessage()." [A".__LINE__."]";
									break;
								}
								// check if client is online and kick if they are
								try {
									$online = $ts3_VirtualServer->clientGetIdsByUid($tsUniqueID);
									echo "Client online. Attempting kick... ";
									try {
										$ts3_VirtualServer->clientGetByUid($tsUniqueID)->Kick(TeamSpeak3::KICK_SERVER, "Teamspeak access revoked by ".$_SESSION["EVEOTSusername"].".");
										echo "Kicked.<br />Deleting client from Teamspeak... ";
									} catch (TeamSpeak3_Exception $e) {
										echo "FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]";
										break;
									}
								} catch(Teamspeak3_Exception $e) {
									echo "Client offline.<br />Deleting from Teamspeak... ";
								}
								// delete client from Teamspeak
								try {
									$ts3_VirtualServer->clientdeleteDb($tsDatabaseID);
									echo "Done.<br />";
									// delete client from database
									try {
										echo "Deleting client from user database... ";
										$queryDelete = "DELETE FROM users WHERE tsDatabaseID = '".$tsDatabaseID."';";
										SQLconnect("open");
											mysql_query($queryDelete);
											echo "Done.<br /><br />All operations completed successfully, \"".$tsName."\" has been removed.<br /><br />";
											echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />";
											// START Log
											$timestamp = gmdate('d.m.Y H:i');
											$timestamp = mysql_real_escape_string($timestamp);
											$log = mysql_real_escape_string($_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")");
											$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
											// END Log
										SQLconnect("close");
									} catch (TeamSpeak3_Exception $e) {
										echo "FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />";
									}
								} catch (TeamSpeak3_Exception $e) {
									if ($e->getMessage() == "invalid clientID") {
										echo "Client did not exist on Teamspeak. (".$tsDatabaseID.")<br />";
										try {
											echo "Deleting client from user database... ";
											$queryDelete = "DELETE FROM users WHERE tsDatabaseID = '".$tsDatabaseID."';";
											SQLconnect("open");
												mysql_query($queryDelete);
												echo "Done.<br /><br />All operations completed successfully, \"".$tsName."\" has been removed.<br /><br />";
												echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />";
												// START Log
												$timestamp = gmdate('d.m.Y H:i');
												$timestamp = mysql_real_escape_string($timestamp);
												$log = mysql_real_escape_string($_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")");
												$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
												// END Log
											SQLconnect("close");
										} catch (TeamSpeak3_Exception $e) {
											echo "FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />";
										}
									} else {
										echo "FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]<br />";
									}
								}
							}
						}
					break;
					case "members_discrepancies":
				        // Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							// Discrepancies
							if (!isset($_GET["run"])) {
								// Confirm run discrepancies
								echo "Discrepancy check between the SQL user database and the Teamspeak user database for redundant entries and desynchronisation.<br /><br /><strong>NOTE:</strong> For each member in the SQL database, the Teamspeak server will be contacted and asked if the entry is redundant. Depending on the amount of members you have, this can take time. Do not leave the page.<br /><br />";
								SQLconnect("open");
									$memberCount = 0;
									$query = mysql_query("SELECT entryID FROM users;");
									while ($row = mysql_fetch_array($query)) {
										$memberCount = $memberCount + 1;
									}
								SQLconnect("close");
								echo "<center><strong>".$memberCount."</strong> records will be checked for discrepancies.<br /> <a href=\"admin_panel.php?menu=members_discrepancies&run\"><input type=\"button\" value=\"Confirm\" /></a></center>";
							} else {
								// Run discrepancies
								echo "Scanning for discrepancies...<br />";
								echo "<div id=\"prog_bar\"></div>";
								SQLconnect("open");
									// Get total members
									$queryCount = mysql_query("SELECT entryID FROM users;");
									$memberCount = 0;
									while ($row = mysql_fetch_array($queryCount)) {
										$memberCount = $memberCount + 1;
									}
									// Connection ready
									try {
										$ts3_VirtualServer = TeamSpeak3::factory("serverquery://".$c->tsname.":".$c->tspass."@".$c->tshost.":".$c->tsport."/?server_port=".$c->tscport);
									} catch (TeamSpeak3_Exception $e) {
										echo "Error: ".$e->getMessage()." [A".__LINE__."]";
										break;
									}
									// Discrepancies
									$queryDiscrepancy = mysql_query("SELECT entryID,characterID,blue,tsDatabaseID,tsUniqueID,tsName FROM users ORDER BY entryID ASC;");
									$progress = 0;
									$discrepancies = 0;
									while ($row = mysql_fetch_array($queryDiscrepancy)) {
										// Update progress bar
										$progress = $progress + 1;
										echo "<script type=\"text/javascript\">prog_bar(".$progress.",0,".$memberCount.",400,10,\"#465674\",\"#2FC020\");</script>";
										// Run checks
										// Gather resources
										$entryID = "$row[entryID]";
										$characterID = "$row[characterID]";
										$blue = "$row[blue]";
										$tsDatabaseID = "$row[tsDatabaseID]";
										$tsUniqueID = "$row[tsUniqueID]";
										$tsName = "$row[tsName]";
										try {
											// Check if the user is in the Teamspeak database
											$ts3_VirtualServer->clientGetNameByDbid($tsDatabaseID);
										} catch (TeamSpeak3_Exception $e) {
											if ($e->getMessage() == "invalid clientID") {
												// Discrepancy found
												if ($discrepancies == 0) {
													echo "<form action=\"admin_panel.php?menu=members_delete&discrepancies\" method=\"post\">";
													echo "<table width=\"100%\" border=\"0\"> <tr><td width=\"70\">SQL Entry</td> <td width=\"32\"></td> <td></td> <td width=\"110\">TS Database ID</td> <td align=\"center\" class=\"select-all-checkbox\"><input type=\"checkbox\" /></td></tr>";
												}
												if ($blue == "Yes") {
													$icon = "images/blue.png";
												} else {
													$icon = "images/ally.png";
												}
												echo "<tr bgcolor=\"#151515\"><td>".$entryID."</td> <td><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td> <td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td> <td align=\"center\">".$tsDatabaseID."</td> <td align=\"center\" class=\"select-checkbox\"><input type=\"checkbox\" name=\"discrepancyDelete[]\" value=\"".$entryID."\" /></td></tr>";
												$discrepancies = $discrepancies + 1;
											} else {
												echo "Error: ".$e->getMessage()." [A".__LINE__."]";
												break;
											}
										}
									}
									if ($discrepancies > 0) {
										echo "<tr><td align=\"right\" colspan=\"5\"><br /><input type=\"submit\" name=\"Submit\" value=\"Delete selected\" /></td></tr>";
										echo "</table>";
										echo "</form><br />";
										echo $discrepancies." discrepancies found.";
									} else {
										echo "No discrepancies found, both databases appear to be synchronised.";
									}
								SQLconnect("close");
							}
						}
					break;
					case "members_edit":
						// Authorised to access this area?
				        // Connect to server and select database.
						SQLconnect("open");
							$query = mysql_query("SELECT id,securityLevel FROM admins WHERE username = \"".$_SESSION["EVEOTSusername"]."\";");
							while ($row = mysql_fetch_array($query)) {
								$securityLevel = "$row[securityLevel]";
								$securityID = "$row[id]";
							}
						SQLconnect("close");
						if ($securityLevel !== "1" || $securityID !== $_SESSION["EVEOTSid"]) {
							echo "You are not authorised to access this area.<br />";
							break;
						} else {
							$id = $_GET["id"];
							SQLconnect("open");
								// Who are we editing?
								$query = mysql_query("SELECT tsName FROM users WHERE entryID=$id;");
								while ($row = mysql_fetch_array($query)) {
									$memberName = "$row[tsName]";
								}
							SQLconnect("close");
							// Edit member
							echo "<strong>Editing user:</strong> ".$memberName."<br /><br />";
							// Change TS nickname
							echo "<strong>Change Teamspeak Nickname:</strong><br />";
							if (!isset($_POST["newNick"])) {
								echo "<form action=\"?menu=members_edit&id=$id\" method=\"post\">";
								?>
								<table>
									<tr>
										<td style="text-align: right;"><font size="2">Change to:</font></td>
										<td style="text-align: left;"><input name="newNick" size="40" /></td>
										<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
									</tr>
								</table>
								<?php
								echo "</form>";
							} else if (isset($_POST["newNick"])) {
								// Change the tsName
								if ($_POST["newNick"] == "") {
									echo "Error: New nickname was blank. Please enter their new name including any tickers.<br /><br />";
									echo "<form action=\"?menu=members_edit&id=$id\" method=\"post\">";
									?>
									<table>
										<tr>
											<td style="text-align: right;"><font size="2">Change to:</font></td>
											<td style="text-align: left;"><input name="newNick" size="40" /></td>
											<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
										</tr>
									</table>
									<?php
									echo "</form>";
									break;
								} else {
									echo "Changing members nickname...<br />";
									// Connect to the database and UPDATE tsName
									if (strlen($_POST["newNick"]) > 30) {
										echo "Teamspeaks max nickname length exceeded (30 characters), cropping nickname...<br />";
									}
									$newNick = substr($_POST["newNick"], 0, 30);
									SQLconnect("open");
										$queryCheck = mysql_query("SELECT tsName FROM users WHERE entryID=\"$id\";");
										while ($row = mysql_fetch_array($queryCheck)) {
											$currentNick = "$row[tsName]";
										}
										if ($currentNick == $newNick) {
											echo $currentNick."'s name is already ".$newNick.", nothing was changed.<br />";
										} else {
											$update = mysql_query("UPDATE users SET tsName=\"$newNick\" WHERE entryID=\"$id\";");
											// START Log
											$timestamp = gmdate('d.m.Y H:i');
											$timestamp = mysql_real_escape_string($timestamp);
											$log = $_SESSION["EVEOTSusername"]." changed ".$memberName."'s Teamspeak name to ".$newNick.".";
											$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
											// END Log
											echo $memberName."'s permitted nickname in Teamspeak has been changed.<br />";
										}
									SQLconnect("close");
									echo "<form action=\"?menu=members_edit&id=$id\" method=\"post\">";
									?>
									<table>
										<tr>
											<td style="text-align: right;"><font size="2">Change to:</font></td>
											<td style="text-align: left;"><input name="newNick" size="40" /></td>
											<td colspan="6" style="text-align: right;"><input name="submit" type="submit" value="Change" /></td>
										</tr>
									</table>
									<?php
									echo "</form>";
								}
							}
						}
					break;
					case "whitelist":
				        echo "If a corporation is in an alliance it is advised <em>not</em> to give the corporation access, but to give the alliance the access. Also remember to keep an eye out for corporations with access joining alliances that shouldn't have it, you should remove these corporations.<br /><strong>TL;DR:</strong> All corporations in your corporation list should NOT be in an alliance.<br /><br />";
				        ?>		
							<form action="?menu=whitelist_add&type=alliance" method="post">
							<table>
								<tr>
									<td style="text-align: right;"><font size="2">Alliance Name:</font></td>
									<td style="text-align: left;"><input name="allianceName" size="16"/></td>
									<td colspan="2" style="text-align: right;"><input name="submit" type="submit" value="Add Alliance" /></td>
								</tr>
							</table>
							</form>
							<form action="?menu=whitelist_add&type=corp" method="post">
							<table>
								<tr>
									<td style="text-align: right;"><font size="2">Corporation Name:</font></td>
									<td style="text-align: left;"><input name="corpName" size="16"/></td>
									<td colspan="2" style="text-align: right;"><input name="submit" type="submit" value="Add Corporation" /></td>
								</tr>
							</table>
							</form>
						<?php
						echo "The following have access to your Teamspeak 3 server.<br /><br />";
						// Build alliance whitelist
				        SQLconnect("open");
					        $query = mysql_query("SELECT * FROM alliances ORDER BY alliance ASC;");
					        $count = mysql_num_rows($query);
					        if ($count > 0) {
						        echo "<table width=\"100%\" border=\"0\">";
								echo "<tr><td width=\"32px\"></td><td>Alliances</td><td width=\"75px\" align=\"center\">Members</td><td width=\"40px\"></td></tr>";
								// Populate alliance table
								while ($row = mysql_fetch_array($query)) {
									$id = "$row[id]";
									$allianceID = "$row[allianceID]";
									$alliance  = "$row[alliance]";
									$allianceMembers  = "$row[memberCount]";
									echo "<tr><td width=\"32px\"><img src=\"http://image.eveonline.com/Alliance/".$allianceID."_32.png\" border=\"0\"></td><td>".$alliance."</td><td align=\"right\">".number_format($allianceMembers)."</td><td align=\"right\"><a href=\"?menu=whitelist_delete&type=alliance&id=".$id."\" onclick=\"return confirm('Are you sure you want to remove ".$alliance."?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td></tr>";
								}
								echo "</table><br />";
							}
				        SQLconnect("close");
				        // Build corporation whitelist
				        SQLconnect("open");
					        $query = mysql_query("SELECT * FROM corporations ORDER BY corp ASC;");
					        $count = mysql_num_rows($query);
					        if ($count > 0) {
						        echo "<table width=\"100%\" border=\"0\">";
								echo "<tr><td width=\"32px\"></td><td>Corporations</td><td width=\"75px\" align=\"center\">Members</td><td width=\"40px\"></td></tr>";
								// Populate corporation table
								while ($row = mysql_fetch_array($query)) {
									$id = "$row[id]";
									$corpID = "$row[corpID]";
									$corp = "$row[corp]";
									$corpAlliance = "$row[corpAlliance]";
									$corpMembers = "$row[memberCount]";
									echo "<tr><td width=\"32px\"><img src=\"http://image.eveonline.com/Corporation/".$corpID."_32.png\" border=\"0\"></td><td>".$corp."<br /><font size=\"2\">".$corpAlliance."</font></td><td align=\"right\">".number_format($corpMembers)."</td><td align=\"right\"><a href=\"?menu=whitelist_delete&type=corp&id=".$id."\" onclick=\"return confirm('Are you sure you want to remove ".$corp."?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td></tr>";
								}
								echo "</table>";
							}
				        SQLconnect("close");
					break;
					case "whitelist_add":
						$type = $_GET["type"];
						if (!isset($_POST["corpName"])) {
							$_POST["corpName"] = "";
						}
						if (!isset($_POST["allianceName"])) {
							$_POST["allianceName"] = "";
						}
						if ($_POST["corpName"] == "" && $_POST["allianceName"] == "") {
							echo "Error: No Alliance or Corporation defined.<br /><br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
							break;
						}
						if ($type == "alliance") {
							$allianceName = $_POST["allianceName"];
							// Check if the alliance is already on the whitelist
							SQLconnect("open");
								$queryCheckExists = mysql_query("SELECT * FROM alliances WHERE alliance = \"$allianceName\";");
								$count = mysql_num_rows($queryCheckExists);
								while ($row = mysql_fetch_array($queryCheckExists)) {
									$dbAllianceName = "$row[alliance]";
								}
							SQLconnect("close");
							if ($count > 0) {
								echo "Error: ".$dbAllianceName." is already on the whitelist.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Set Pheal to AllianceList.xml
							try {
								$allianceList = $pheal->eveScope->AllianceList();
							} catch (PhealException $E) {
								echo "An error occured: ".$E->getMessage();
								break;
							}
							// Scan through the alliances
							foreach($allianceList->alliances as $a) {
								// skip if alliance doesn't match
								if(strtolower($a->name) == strtolower($allianceName)) {
									// store AllianceList details
									$allianceName = $a->name;
									$allianceID  = $a->allianceID;
									$allianceMembers = $a->memberCount;
									break;
								}
							}
							if ($allianceID == "" || $allianceID == NULL) {
								echo "Error: Couldn't find the alliance. Please check in-game if the spelling is correct (copy/paste is your friend).<br /><br />You entered: ".$_POST["allianceName"]."<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else {
								SQLconnect("open");
									$SQLallianceName = mysql_real_escape_string($allianceName);
									$queryINSERT = mysql_query("INSERT INTO alliances (alliance, allianceID, memberCount) VALUES (\"$SQLallianceName\", $allianceID, $allianceMembers);");
									if (!$queryINSERT) {
									    die('Invalid query: ' . mysql_error());
									}
									// START Log
									$timestamp = gmdate('d.m.Y H:i');
									$timestamp = mysql_real_escape_string($timestamp);
									$log = $allianceName." was added to the alliance whitelist by ".$_SESSION["EVEOTSusername"].".";
									$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
									// END Log
								SQLconnect("close");
								echo $allianceName." was added to the whitelist.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
							}
						} else if ($type == "corp") {
							$corpName = $_POST["corpName"];
							// Check if the corp is already on the whitelist
							SQLconnect("open");
								$queryCheckExists = mysql_query("SELECT * FROM corporations WHERE corp = \"$corpName\";");
								$count = mysql_num_rows($queryCheckExists);
								while ($row = mysql_fetch_array($queryCheckExists)) {
									$dbCorpName = "$row[corp]";
								}
							SQLconnect("close");
							if ($count > 0) {
								echo "Error: ".$dbCorpName." is already on the whitelist.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Get the CorporationID
							try {
								$fetch = $pheal->eveScope->CharacterID(array('names' => $corpName));
								foreach($fetch->characters as $character) {
									$corpID = $character->characterID;
								}
							} catch (PhealException $e) {
								die("An error occured: ".$e->getMessage()." [A".__LINE__."]");
							}
							if ($corpID == "" || $corpID == NULL) {
								echo "Error: Couldn't find the corporation. Please check in-game if the spelling is correct (copy/paste is your friend).<br /><br />You entered: ".$_POST["corpName"]."<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							} else {
								// Get the corp details
								try {
									$fetch = $pheal->corpScope->CorporationSheet(array('corporationID' => $corpID));
									$corpName = $fetch->corporationName;
									$corpMembers = $fetch->memberCount;
								} catch (PhealException $e) {
									die("An error occured: ".$e->getMessage()." [A".__LINE__."]");
								}
								SQLconnect("open");
									$SQLcorpName = mysql_real_escape_string($corpName);
									$queryINSERT = mysql_query("INSERT INTO corporations (corp, corpID, memberCount) VALUES (\"$SQLcorpName\", $corpID, $corpMembers);");
									if (!$queryINSERT) {
									    die('Invalid query: ' . mysql_error());
									}
									// START Log
									$timestamp = gmdate('d.m.Y H:i');
									$timestamp = mysql_real_escape_string($timestamp);
									$log = $corpName." was added to the corporation whitelist by ".$_SESSION["EVEOTSusername"].".";
									$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
									// END Log
								SQLconnect("close");
								echo $corpName." was added to the whitelist.<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
							}
						} else {
							echo "Error: Type is not defined.<br /><br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
							break;
						}
					break;
					case "whitelist_delete":
						$id = $_GET["id"];
						$type = $_GET["type"];
						if ($type == "alliance") {
							// Page refreshed? ID Already deleted?
							SQLconnect("open");
								$query = mysql_query("SELECT * FROM alliances WHERE id=$id;");
								$count = mysql_num_rows($query);
							SQLconnect("close");
							if ($count == 0) {
								echo "Error: Couldn't find the alliance in the database, already deleted?<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Delete Alliance
							SQLconnect("open");
								$query = mysql_query("SELECT alliance FROM alliances WHERE id=$id;");
								while ($row = mysql_fetch_array($query)) {
									$alliance = "$row[alliance]";
								}
								echo "<strong>Removing alliance...</strong><br />Alliance: ".$alliance."<br /><br />";
								$queryDelete = mysql_query("DELETE FROM alliances WHERE id=$id;");
								// START Log
								$timestamp = gmdate('d.m.Y H:i');
								$timestamp = mysql_real_escape_string($timestamp);
								$log = $alliance." was removed from the alliance whitelist by ".$_SESSION["EVEOTSusername"].".";
								$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
								// END Log
							SQLconnect("close");
							echo "Alliance removed.<br /><br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
						} else if ($type == "corp") {
							// Page refreshed? ID Already deleted?
							SQLconnect("open");
								$query = mysql_query("SELECT * FROM corporations WHERE id=$id;");
								$count = mysql_num_rows($query);
							SQLconnect("close");
							if ($count == 0) {
								echo "Error: Couldn't find the corporation in the database, already deleted?<br /><br />";
								echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
								break;
							}
							// Delete Corporation
							SQLconnect("open");
								$query = mysql_query("SELECT corp FROM corporations WHERE id=$id;");
								while ($row = mysql_fetch_array($query)) {
									$corp = "$row[corp]";
								}
								echo "<strong>Removing corporation...</strong><br />Corporation: ".$corp."<br /><br />";
								$queryDelete = mysql_query("DELETE FROM corporations WHERE id=$id;");
								// START Log
								$timestamp = gmdate('d.m.Y H:i');
								$timestamp = mysql_real_escape_string($timestamp);
								$log = $corp." was removed from the corporation whitelist by ".$_SESSION["EVEOTSusername"].".";
								$log = mysql_query("INSERT INTO logs (time, log) VALUES (\"$timestamp\", \"$log\");");
								// END Log
							SQLconnect("close");
							echo "Corporation removed.<br /><br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
						} else {
							echo "Error: Type is not defined.<br /><br />";
							echo "<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />";
							break;
						}
					break;
				}
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="50px">
			<?php
				$link = magicLink("https://gate.eveonline.com/Profile/MJ%20Maverick","935338328","MJ Maverick");
				echo "
				<div class='footer'>
					<br />
					<span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by ".$link."<br />
					Powered by the TS3 PHP Framework & Pheal<br /></span>
					<span style='font-size: 10px;'>EVEOTS $v->release</span>
				</div>";
			?>
		</td>
	</tr>
</table>
</body>
</html>