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

//Activate ESI API Namepsaces
use Seat\Eseye\Cache\NullCache;
use Seat\Eseye\Configuration;
use Seat\Eseye\Containers\EsiAuthentication;
use Seat\Eseye\Eseye;
use Seat\Eseye\Log;

//Activate Classes
$config = new \EVEOTS\Config\Config();
$version = new \EVEOTS\Version\Version();
$session = new \Custom\Session\Sessions();

//Prepare logging for ESI API
$log = new Seat\Eseye\Log\FileLogger();
// Prepare an authentication container for ESI
$authentication = PrepareESIAuthentication();
// Instantiate a new ESI instance.
$esi = new Eseye($authentication);


if(!isset($_SESSION['EVEOTSusername'])) {
    $username = "";
    header("location:index.php");
} else {
    $username = $_SESSION['EVEOTSusername'];
}

//Connect to the database
$db = DBOpen();

?>

<html>
    <head>
        <!--metas-->
        <meta content="text/html" charset="utf-8" http-equiv="Content-Type">
        <meta content="EVEOTS V2 Admin Panel" name="description">
        <meta content="index,follow" name="robots">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <title>EVEOTS V2 Admin Panel</title>
        <link href="/../css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="/../images/banner.jpg" type="image/x-icon">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    </head>
    <body>
        <?php
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
                        printf("<div class=\"container\">
                                    <div class=\"panel-default\">
                                        <div class=\"panel-heading\">
                                            <h2>Warning</h2>
                                        </div>
                                        <div class=\"panel-body\">
                                            Warning: Setup not complete!<br>
                                            User \"admin\" is still in the database.<br>
                                            This is a massive security risk.<br>
                                            Please create yourself a new account, set it as the root admin and delete the default \"admin\" account immediately.<br>
                                            See the readme \"How to Setup A New Root Admin\" for more detailed instructions.                    
                                        </div>
                                    </div>
                                </div>");
                    }
                    PrintMainPanel();
                    $admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
                    PrintAdminTable($db, $esi, $admins, $log, $config);
                    break;
                case "change_password":
                    if(!isset($_POST['newPassword'])) {
                        PrintChangePassword();
                    } else {
                        if($_POST['newPassword'] == "" || $_POST['newPConfirm'] == "") {
                            printf("Error: Please fill both fields.<br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if ($_POST['newPassword'] != $_POST['newPConfirm']) {
                            printf("Error: The passwords did not match.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if (preg_match("/^[a-zA-Z0-9]+$/", $_POST["newPassword"]) == 0) {
                            printf("Error: Passwords can only contain A-Z, a-z and 0-9.<br /><br />");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else {
                            $newPassword = md5(filter_input('POST', 'newPassword'));
                            $sid = $_SESSION['EVEOTSid'];
                            ChangePassword($db, $newPassword. $sid, $username);
                        }
                    }
                    break;
                case "logs":
                    if($_SESSION['EVEOTSid'] == $config->GetAdminID()) {
                        printf("<form class=\"form-control\" method=\"POST\" action=\"?menu=logs\">");
                        printf("<label>Root Administrator Option: </label>");
                        printf("<input class=\"form-control\" type=\"submit\" value=\"Clear Logs\" onclick=\"return confirm('Are you sure you want clear all logs?')\" />");
                        printf("</form>");
                    }
                    if(isset($_POST['clear_logs'])) {
                        printf("Clearing logs...<br>");
                        $db->executeSql('TRUNCATE logs');
                        printf("Logs cleared.<br><br>");
                    }
                    PrintLogs($db);
                    break;
                case "admins_add":
                    $securityLevel = CheckSecurityLevel($db, $username);
                    if($securityLevel['SecurityLevel'] != "1" || $securityLevel['SecurityID'] != $_SESSION['EVEOTSid']) {
                        printf("You are not authorized to access this area.<br>");
                    } else {
                        //Trim any spaces either side
                        $adminCharacterName = trim(filter_input('POST', 'adminCharacterName'));
                        $adminPassword = trim(filter_input('POST', 'adminPassword'));
                        $adminSecurityLevel = trim(filter_input('POST', 'adminSecurityLevel'));
                        //Make sure all fields are filled in
                        if($adminCharacterName == "") {
                            printf("Character Name cannot be blank.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if($adminPassword == "") {
                            printf("Password cannot be blank.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if ($adminSecurityLevel == "") {
                            printf("Security Level cannot be blank.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        } else if ($adminSecurityLevel != "1" && $adminSecurityLevel != "2") {
                            printf("Security Level must be 1 or 2.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        }
                        //Make sure password is only a-z, A-Z, 0-9
                        if(preg_match("/^[a-zA-Z0-9]+$/", $adminPassword) === 0) {
                            printf("Error: Passwords can only contain A-Z, a-z and 0-9<br /><br />");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        }
                        if(AdminNameInUse($db, $adminCharacterName)) {
                            printf("Error: " . $adminCharacterName . " already has an account.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        }
                        //Check if the Character Name is legit and get the Character ID
                        $adminCharacterID = CharacterNameToID($db, $esi, $log, $adminCharacterName);
                        if($adminCharacterID == 0) {
                            printf("According to the CCP ESI server the character " . $adminCharacterName . " does not exist.<br><br>");
                            printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                            break;
                        }
                        printf("<strong>Adding administrator...</strong><br>Character: " . $adminCharacterName . "<br>Password: " . $adminPassword . "<br><Security Level: " . $adminSecurityLevel . "<br>Character ID: " . $adminCharacterID . "<br><br>");
                        //Insert the admin into the database
                        $db->insert('Admins', array('username' => $adminCharacterName, 'password' => md5($adminPassword), 'characterID' => $adminCharacterID, 'securityLevel' => $adminSecurityLevel));
                        //Insert a log entry
                        $timestamp = gmdate('d.m.Y H:i');
                        $log = $admincharacterName . "was given an administrator account (SL " . $adminSecurityLevel . ") by " . $_SESSION['EVEOTSusername'] . ".";
                        AddLogEntry($db, $timestamp, $entry);
                        printf("Administrator added.<br>");
                        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                    }
                    break;
                case "admins_audit":
                    $security = CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
                    if($security['SecurityLevel'] != "1" || $security['SecurityID'] != $_SESSION['EVEOTSid']) {
                        printf("You are not authorized to access this area.<br>");
                        break;
                    } else {
                        PrintAdminAdd($db);
                    }                    
                    break;
                case "admins_delete":
                    $security = CheckSecurityLevel($db, $_SESSION['EVEOTSusername']);
                    if($security['SecurityLevel'] != "1" || $security['SecurityID'] != $_SESSION['EVEOTSid']) {
                        printf("You are not authorized to access this area.<br>");
                        break;
                    }
                    $id = filter_input('GET', 'id');
                    if($id == $config->GetAdminID()) {
                        printf("This is the root admin!  You cannot delete this admin account.<br><br>");
                        break;
                    }
                    $admin = $db->fetchRow('SELECT * FROM Admins WHERE id= :id', array('id' => $id));
                    if($admin == NULL) {
                        printf("Error: Couldn't find the admin in the database.<br><br>");
                        printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                        break;
                    }
                    printf("<strong>Deleting administrator...</strong><br>");
                    $db->delete('Admins', array('id'  => $id));
                    $timestamp = gmdate('d.m.Y H:i');
                    $entry = $admin['username'] . "'s administrator account was deleted. by " . $_SESSION['EVEOTSusername'] . ".";
                    AddLogEntry($db, $timestamp, $entry);
                    printf("Administrator deleted.<br>");
                    printf("<input type=\"button\" value=\"Back\" onclick=\"history.back(-1)\" />");
                    break;
                case "admins_edit":
                    break;
                case "members_audit":
                    break;
                case "members_delete":
                    break;
                case "members_discrepancies":
                    break;
                case "members_edit":
                    break;
                case "whitelist":
                    break;
                case "whitelist_add":
                    break;
                case "whitelist_delete":
                    break;
            };
            
            printf("<div class='footer'>
                        <br />
                        <span style='font-size: 11px;'>Teamspeak 3 Registration for EVE Online by ".$link."<br />
                        Powered by the TS3 PHP Framework & Pheal<br /></span>
                        <span style='font-size: 10px;'>EVEOTS $v->release</span>
                    </div>");
        ?>        
        
        
        
          
        
    </body>
</html>