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
        //Print out the white list form
        PrintWhiteListForm();
        
        //Build the white list to print out on the screen
        $whiteList = $db->fetchRowMany('SELECT * FROM Blues');
        PrintWhiteList($whiteList);
        break;
    case "whitelist_add":
        $type = $_GET["type"];
        if (!isset($_POST["corpName"])) {
            $corporationName = "";
        } else {
            $corporationName = filter_input('POST', 'corpName');
        }
        if (!isset($_POST["allianceName"])) {
            $allianceName = "";
        } else {
            $allianceName = filter_input('POST', 'allianceName');
        }
        if(!isset($_POST['charName'])) {
            $characterName = "";
        } else {
            $characterName = filter_input('POST', 'charName');
        }
        if ($corporationName == "" && $allianceName == "" && $characterName == "") {
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