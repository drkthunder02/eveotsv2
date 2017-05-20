<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON DJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
ini_set('date.timezone', 'Europe/London');
error_reporting(E_ALL | E_STRICT);

//Required files
require_once __DIR__.'/../functions/registry.php';

//Activate Classes
$config = new \EVEOTS\Config\Config();
$esiConfig = $config->GetESIConfig();
$v = new \EVEOTS\Version\Version();
$version = $v->version;
$session = new \Custom\Session\Sessions();
$esi = new \EVEOTS\ESI\ESI();

if(!isset($_SESSION['EVEOTSusername'])) {
    $username = "";
    header("location:index.php");
} else {
    $username = $_SESSION['EVEOTSusername'];
}

//Connect to the database
$db = DBOpen();

//Open HTML Tags, then print header, then open the body tag
printf("<html>");
PrintHTMLHeader();
printf("<body>");
//Print the navigation bar
PrintNavBar($username); 
$queryAdmin = $db->fetchColumn('SELECT username FROM Admins WHERE username=: user', array('user' => 'admin'));
$count = $db->getRowCount();
if($count > 0) {
    $installAccount = true;
} else {
    $installAccount = false;
}
if(isset($_GET['menu'])) {
    $menu = filter_input('GET', 'menu');
} else {
    $menu = "main";
}

switch($menu) {
    case "main":
        if($installAccount == true) {
            printf("<div class=\"container\">");
            printf("Warning: Setup not complete.<br>Users \"admin\" is still in the database.<br>");
            printf("This is a massive security risk.<br>");
            printf("Please create yourself a new account, set it as the root admin, then delete the default \"admin\" account immediately.<br>");
            printf("</div>");
        }
        printf("<div class=\"container\">");
        printf("Welcome to your EVEOTS admin panel, here you can manage admins along with which Corporations and Alliances can access your Teamspeak 3 server.<br /><br />");
        printf("Here are a few things you should know:<br />");
        printf("<ul>");
        printf("<li><strong>Root Administrator -</strong> This person should be the main administrator. This user cannot be deleted or edited by other administrators. This person will be highlighted wherever administrators are listed.<br /><font size=\"2\">To change the root admin from default (recommended) you need to go into the \"admins\" table in the database and get the \"id\" number of the root admin to-be. Then open config.php and set \$adminID as that number.</font><br /><br /></li>");
        printf("<li><strong>Security Level (SL) -</strong> This is the level of control someone has.<br /> 1 = Super admin. Able to edit, delete and create others via the Audit menus.<br /> 2 = Normal admin. Can only make changes to the whitelist. Cannot access the audit menus (recommended).<br /><br /></li>");
        printf("<li><strong>Administrator Lists -</strong> The corporation/alliance listed is LIVE. So keep an eye out for one of your admins leaving your corporation or alliance and still having access.<br /><br /></li>");
        printf("</ul>");
        printf("<strong>Administrators:</strong><br />");
        printf("</div>");
        //Print out a container with a list of admins in it
        $admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
        PrintAdminTable($admins, $esi);
        break;
    case "change_password":
        if(!isset($_POST["newPassword"])) {
            printf("<div class=\"container\">");
            printf("Passwords can only contain A-Z, a-z, and 0-9.<br><br>");
            printf("<form class=\"form-group\" action=\"?menu=change_password\" method=\"POST\">");
            printf("<label for=\"newPassword\">Password:</label>");
            printf("<input class=\"form-control\" type=\"password\" name=\"newPassword\" id=\"newPassword\" size=\"16\">");
            printf("<label for=\"newPConfirm\">Confirm:</label>");
            printf("<input class=\"form-control\" type=\"password\" name=\"newPConfirm\" id=\"newPConfirm\" size=\"16\">");
            printf("<br>");
            printf("<input class=\"form-control\" name=\"submit\" type=\"submit\" value=\"Change PW\">");
            printf("</form>");
            printf("</div>");
        } else {
            $newPassword = filter_input(INPUT_POST, "newPassword");
            $newPConfirm = filter_input(INPUT_POST, "newPConfirm");
            AdminChangePassword($newPassword, $newPConfirm, $db);
        }
        break;
    case "logs":
        if($_SESSION["EVEOTSid"] == $config->GetAdminID()) {
            printf("<div class=\"container\">");
            printf("<form class=\"form-group\" action=\"?menu=logs\" method=\"post\">");
            printf("<label for=\"clear_logs\">Root Administrator Option:</label>");
            printf("<input class=\"form-control\" name=\"clear_logs\" id=\"clear_logs\" type=\"submit\" value=\"Clear Logs\" onclick=\"return confirm('Are you sure you want to clear all logs\">");
            printf("</form>");
            printf("</div>");
        }
        if(isset($_POST["clear_logs"])) {
            printf("<div class=\"container\">");
            $db->executeSql('TRUNCATE logs');
            printf("Logs cleared.<br><br>");
            printf("</div>");
        }
        PrintAdminLogs($db);
        break;
    case "admins_add":
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        //Trim any spaces either side
        $adminCharacterName = trim(filter_input(INPUT_POST, 'adminCharacterName'));
        $adminPassword = trim(filter_input(INPUT_POST, 'adminPassword'));
        $adminSecurityLevel = trim(filter_input(INPUT_POST, 'adminSecurtyLevel'));
        AdminAdd($db, $adminCharacterName, $adminPassword, $adminSecurityLevel);
        break;
    case "admins_audit":
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        // Audit
        printf("<div class=\"container\">");
        printf("<strong>Add Administrator:</strong><br>");
        printf("Enter the Character ID you wish to add as an Administrator in the form below.<br>");
        printf("<form class=\"form-group\" action=\"?menu=admins_add\" method=\"POST\">");
        printf("<label for=\"adminCharacterId\">Character ID:</label>");
        printf("<input class=\"form-control\" type=\"text\" name=\"adminCharacterId\" id=\"adminCharacterId\">");
        printf("<label for=\"adminPassword\">Password:</label>");
        printf("<input class=\"form-control\" type=\"text\" name=\"adminPassword\" id=\"adminPasswrd\">");
        printf("<label for=\"adminSecurityLevel\">Security Level (1/2):</label>");
        printf("<input class=\"form-control\" type=\"text\" name=\"adminSecurityLevel\" id=\"adminSecurityLevel\">");
        printf("<br>");
        printf("<input class=\"form-control\" type=\"submit\" name=\"submit\ id=\"submit\" value=\"Add\">");
        printf("</form>");
        printf("</div>");
        $admins = $db->fetchRowMany('SELECT * FROM admins ORDER BY username');
        printf("<table class=\"table table-striped\">");
        printf("<thead>");
        printf("<tr>");
        printf("<td></td><td>Username</td><td>Security Level</td><td></td>");
        printf("</tr>");
        printf("</thead>");
        foreach($admins as $admin) {
            printf("<tr>");
            if($admin['characterID'] == "") {
                printf("<td><img src=\"images/admin.png\" border=\"0\">");
            } else {
                printf("<td><img src=\"http://image.eveonline.com/Character/" . $admin['characterID'] . "_32.jpg\" border=\"0\"></td>");
            }
            printf("<td>" . $admin['username'] . "</td>");
            printf("<td>" . $admin['securityLevel'] . "</td>");
            if($admin['characterID'] == $config->GetAdminID() && $admin['characterID'] == $_SESSION['EVEOTSid']) {
                printf("<td><a href=\"?menu=admins_edit&id=\"" . $admin['characterID'] . "\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a></td>");
            } else if ($admin['characterID'] == $config->GetAdminID()) {
                printf("<td></td>");
            } else {
                printf("<td><a href=\"?menu=admins_edit&id=\"" . $admin['characterID'] . "\"><img src=\"images/edit.png\" border=\"0\" title=\"Edit\"></a><a href=\"?menu=admins_delete&id=\"" . $admin['characterID'] . "\" onclick=\"return confirm('Are you sure you want to delete " . $admin['username'] . "?')\"><img src=\"images/delete.png\" border=\"0\" title=\"Delete\"></a></td>");
            }
            printf("</tr>");
        }
        printf("</table>");
        break;
    case "admins_delete":
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        $id = filter_input(INPUT_GET, "id");
        if($id == $config->GetAdminID()) {
            printf("This is the root admin!  You cannot delete this user.");
        }
        $admin = $db->fetchRow('SELECT * FROM Admins WHERE id= :id', array('id' => $id));
        if($db->getRowCount() == 0) {
            printf("Error: Couldn't find the admin in the database.<br>");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        }
        printf("<strong>Deleting administrator...</strong><br>");
        printf("Character: " . $admin['username'] . "<br>");
        printf("Security Level: " . $admin['securityLevel'] . "<br><br>");
        $db->delete('Admins', array('id' => $admin['id']));
        $entry = $admin['username'] . "'s administrator account was deleted by " . $_SESSION['EVEOTSusername'] . ".";
        AddLogEntry($db, gmdate('d.m.Y H:i'), $entry);
        printf("Administrator deleted.<br>");
        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
        break;
    case "admins_edit":
        $id = filter_input(INPUT_GET, "id");
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        //Are we going to edit the root admin
        if($id == $config->GetAdminID() && $_SESSION['EVEOTSid'] == $config->GetAdminID()) {
            printf("You are not permitted to edit the root admin!<br>");
            break;
        }
        //Are we going to edit the root admin
        if($id == $config->GetAdminID()) {
            $rootAdminEdit = true;
        } else {
            $rootAdminEdit = false;
        }
        //Edit admin
        printf("<strong>Editing user:</strong><br>");
        //Change password
        printf("<strong>Change password:</strong><br>");
        if(!isset($_POST['newPassword'])) {
            printf("<form action=\"?menu=admin_edit&id=" . $id . "\" method=\"POST\">");
            printf("<table>
                        <tr>
                                <td style=\"text-align: right;\"><font size=\"2\">New Password:</font></td>
                                <td style=\"text-align: left;\"><input type=\"password\" name=\"newPassword\" size=\"16\" /></td>
                                <td style=\"text-align: right;\"><font size=\"2\">Confirm:</font></td>
                                <td style=\"text-align: left;\"><input type=\"password\" name=\"newPConfirm\" size=\"16\"/></td>
                                <td colspan=\"6\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Change\" /></td>
                        </tr>
                    </table>");
            printf("</form>");
        } else if(isset($_POST['newPassword'])) {
            // Change the password
            $newPassword = filter_input(INPUT_POST, "newPassword");
            $newPConfirm = filter_input(INPUT_POST, "newPConfirm");
            if ($newPassword == "" || $newPConfirm == "") {
                    printf("Error: Please fill both password fields. Type the desired password then confirm it by typing it again in the \"Confirm\" field.<br /><br />");
                    printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                    break;
            } else if ($newPassword != $newPConfirm) {
                    printf("Error: The new passwords do not match.<br /><br />");
                    printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                    break;
            } else if (preg_match("/^[a-zA-Z0-9]+$/", $newPassword) == 0) {
                    // Make sure password is only a-z A-Z 0-9
                    printf("Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />");
                    printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                    break;
            } else {
                    echo "Changing password...<br />";
                    $newpass = md5($_POST["newPassword"]);
                    $db->update('Admins', array('id' => $id), array('password' => $newPass));
                    
                    $timestamp = gmdate('d.m.Y H:i');
                    $log = $_SESSION["EVEOTSusername"] . " changed " . $username . "'s password.";
                    AddLogEntry($db, $timestamp, $log);
                    printf($username . "'s password has been changed.<br />");
            }
        }
        //Change security level
        printf("<strong>Change security level:</strong><br>");
        $newSL = filter_input(INPUT_POST, "newSL");
        if($rootAdminEdit == true) {
            printf("The root admins security level cannot be changed.<br>");
        } else if(!isset($_POST['newSL'])) {
            printf("Error: Security level was blank. Please input 1 or 2.<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            break;
        } else if($newSL != "1" && $newSL != "2") {
            printf("Error: Security Level must be 1 or 2.<br>");
            printf("<form action=\"?menu=admins_edit&id=" . $id . "\" method=\"POST\">");
            printf("<table>
                        <tr>
                            <td style=\"text-align: right;\"><font size=\"2\">New Security Level (1/2):</font></td>
                            <td style=\"text-align: left;\"><input name=\"newSL\" size=\"1\" /></td>
                            <td colspan=\"6\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Change\" /></td>
                        </tr>
                    </table>");
            printf("</form>");
        } else {
            printf("Changing security level...<br>");
            $newSL = filter_input(INPUT_POST, "newSL");
            $currentSL = $db->fetchColumn('SELECT securityLevel FROM Admins WHERE id=: id', array('id' => $id));
            if($currentSL == $newSL) {
                printf($username . " already has a security level of " . $newSL . ", nothing was changed.<br>");
            } else {
                $db->update('Admins', array('id' => $id), array('securityLevel' => $newSL));
                $timestamp = gmdate('d.m.Y H:i');
                $entry = $_SESSION['EVEOTSusername'] . " changed " . $username . "'s security level to " . $newSL . ".";
                AddLogEntry($db, $timestamp, $entry);
                printf($username . "'s security level has been changed.<br>");
                printf("<form action=\"?menu=admins_edit&id=" . $id . "\" method=\"POST\">");
                printf("<table>
                            <tr>
                                <td style=\"text-align: right;\"><font size=\"2\">New Security Level (1/2):</font></td>
                                <td style=\"text-align: left;\"><input name=\"newSL\" size=\"1\" /></td>
                                <td colspan=\"6\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Change\" /></td>
                            </tr>
                        </table>");
                printf("</form>");
            }
        }
        break;
    case "members_audit":
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        $search = filter_input(INPUT_POST, "search");
        MembersAudit($db, $search);      
        break;
    case "members_delete":
        //Authorized access to this server?
        CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
        if(isset($_GET['discrepancies'])) {
            $discrepancy = filter_input(INPUT_POST, "discrepancyDelete");
            if(empty($discrepancy)) {
                printf("Error: No discrepancies selected to be deleted.<br /><br />");
                printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />");
                break;
            } else {
                printf("Deleting discrepancies...<br /><br />");
                printf("<table width=\"100%\" border=\"0\"> <tr><td width=\"100\">Deleteing...</td> <td width=\"32\"></td> <td></td> <td width=\"110\">TS Database ID</td> <td></td></tr>");
                $deleted = 0;
                foreach($discrepancy as $discrep) {
                    $row = $db->fetchRow('SELECT CharacterID,Blue,TSDatabaseID,TSUniqueID,TSName FROM Users WHERE id= :dis', array('dis' => $discrep));
                    $characterID = $row['CharacterID'];
                    $blue = $row['Blue'];
                    $tsDatabaseID = $row['TSDatabaseID'];
                    $tsUniqueID = $row['TSUniqueID'];
                    $tsName = $row['TSName'];
                    $queryDelete = "DELETE FROM users WHERE entryID = '".$discrep."';";
                    $db->delete('Users', array('id' => $discrep));
                    $deleted++;
                    //Print
                    if($blue == "Yes") {
                        $icon = "images/blue.png";
                    } else {
                        $icon = "images/ally.png";
                    }
                    printf("<tr bgcolor=\"#151515\"><td>".$discrep."</td> <td><img src=\"http://image.eveonline.com/Character/".$characterID."_32.jpg\" border=\"0\"></td> <td><img src=\"".$icon."\" border\"0\"> ".$tsName."<br /><font size=\"2\">Unique ID: ".$tsUniqueID."</font></td> <td align=\"center\">".$tsDatabaseID."</td> <td align=\"center\">Deleted</td></tr>");
                }
                printf("</table><br>");
                printf("Discrepancies removed: " . $deleted . "<br>");
            }
        } else {
            $id = filter_input(INPUT_GET, "id");
            //Gather required details for deleting.
            $row = $db->fetchRow('SELECT tsDatabaseID,tsUniqueID,tsName FROM users WHERE id= :id', array('id' => $id));
            if($db->getRowCount()) {
                printf("This client no longer seems to exist.");
                break;
            }
            $entryID = $row['id'];
            $tsDatabaseID = $row['TSDatabaseID'];
            $tsUniqueID = $row['TSUniqueID'];
            $tsName = $row['TSName'];
            printf("Attempting to remove \"" . $tsName . "\" from Teamspeak.<br><br>");
            //Connect to Teamspeak
            try {
                $ts3_VirtualServer = Teamspeak3::factory($config->GetTSServerQuery());
            } catch (Teamspeak3_Exception $e) {
                printf("Error: " . $e->getMessage() . " [A" . __LINE__ . "]");
                break;
            }
            //Check if client is online and kick if they are
            try {
                $online = $ts3_VirtualServer->clientGetIdsByUid($tsUniqueID);
                printf("Client online.  Attemping kick...");
                try {
                    $ts3_VirtualServer->clientGetByUid($tsUniqueID)->Kick(TeamSpeak3::KICK_SERVER, "Teamspeak access revoked by ".$_SESSION["EVEOTSusername"].".");
                    printf("Kicked.<br />Deleting client from Teamspeak... ");
                } catch (Exception $ex) {
                    printf("FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]");
                    break;
                }
            } catch (TeamSpeak3_Exception $e) {
                printf("FAILED> (Error: " . $e->getMessage() . ") [A" . __LINE__ . "]");
            }
            //Delete the client from Teamspeak
            try {
                $ts3_VirtualServer->clientdeleteDb($tsDatabaseID);
                printf("Done.<br>");
                //Delete the client from the database
                try {
                    printf("Deleting client from user database...");
                    $db->delete('Users', array('TSDatabaseID' => $tsDatabaseID));
                    $timestamp = gmdate('d.m.Y H:i');
                    $entry = $_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")";
                    AddLogEntry($db, $timestamp, $entry);
                } catch (TeamSpeak3_Exception $e) {
                    printf("FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />");
                }
            } catch (TeamSpeak3_Exception $e) {
                if($e->getMessage() == "invalid clientID") {
                    printf("Client did not exist on Teamspeak. (".$tsDatabaseID.")<br />");
                    try {
                        printf("Deleting client from user database...");
                        $db->delete('Users', array('tsDatabaseID' => $tsDatabaseID));
                        printf("Done.<br><br>All operations completed successfully, \"" . $tsName . "\" has been removed.<br><br>");
                        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-2)\" />");
                        $timestamp = gmdate('d.m.Y H:i');
                        $entry = $_SESSION["EVEOTSusername"]." revoked Teamspeak access from \"".$tsName."\". (".$tsDatabaseID.")";
                        AddLogEntry($db, $timestamp, $entry);
                    } catch (Exception $ex) {
                        printf("FAILED.<br />WARNING: Failed to remove \"".$tsName."\" from the database, entry ".$entryID.". You will need to remove manually. (Error: ".$e->getMessage().") (SQL: ". mysql_error() .") [A".__LINE__."]<br />");
                    }
                } else {
                    printf("FAILED. (Error: ".$e->getMessage().") [A".__LINE__."]<br />");
                }
            }
        }
        break;
    case "members_discrepancies":
        break;
    case "members_edit":
        break;
    case "whitelist":
        //Print out the white list form
        PrintWhiteListForm();
        
        //Build the white list to print out on the screen
        $whiteList = $db->fetchRowMany('SELECT * FROM Blues');
        PrintWhiteList($whiteList);
        break;
    case "whitelist_add":
        $type = $_GET["type"];
        if (!isset($_POST["corpName"])) {
            $corporationName = null;
        } else {
            $corporationName = filter_input('POST', 'corpName');
        }
        if (!isset($_POST["allianceName"])) {
            $allianceName = null;
        } else {
            $allianceName = filter_input('POST', 'allianceName');
        }
        if(!isset($_POST['charName'])) {
            $characterName = null;
        } else {
            $characterName = filter_input('POST', 'charName');
        }
        if ($corporationName == null && $allianceName == null && $characterName == null) {
            printf("<div class=\"container\">");
            printf("Error: No Alliance or Corporation defined.<br /><br />");
            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
            printf("</div>");
            break;
        }
        //Add to the whitelist
        WhiteListAdd($allianceName, $corporationName, $characterName, $type);
        break;
    case "whitelist_delete":
        //Delete from the whitelist
        $id = filter_input(INPUT_GET, 'id');
        $type = filter_input(INPUT_GET, 'type');
        if($type == "alliance") {
            $entityeType = 3;
        } else if ($type == "corp") {
            $entityType = 2;
        } else if($type == "char") {
            $entityType = 1;
    }
        WhiteListDelete($db, $id, $entityType);
        break;
};

printf("<div class='footer'>
            <br />
            <span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by ".$link."<br />
            Powered by the TS3 PHP Framework & Pheal<br /></span>
            <span style='font-size: 10px;'>EVEOTS $v->release</span>
        </div>");

//Close Body Tag then HTML Tag
printf("</body>");
printf("</html>");

?> 

<table class="table table-striped">
    
</table>