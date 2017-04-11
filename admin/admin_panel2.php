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
$v = new \EVEOTS\Version\Version();
$version = $v->version;
$session = new \Custom\Session\Sessions();

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
        break;
    case "change_password":
        break;
    case "logs":
        break;
    case "admins_add":
        break;
    case "admins_audit":                 
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
        printf("<div class=\"container\">");
        printf("<div class=\"jumbotron\">");
        printf("<h2>If a corporation is in an alliance it is advised <em>not</em> to give the corporation access, but to give the alliance the access. Also remember to keep an eye out for corporations with access joining alliances that shouldn't have it, you should remove these corporations.<br /><strong>TL;DR:</strong> All corporations in your corporation list should NOT be in an alliance.</h2>");
        printf("</div>");
        printf("</div>");
        printf("<div class=\"container\">");
        printf("<form class=\"form-control\" action=\"?menu=whitelist_add&type=alliance\" method=\"post\">
                    <table class=\"table>
                        <tr>
                            <td style=\"text-align: right;\"><font size=\"2\">Alliance Name:</font></td>
                            <td style=\"text-align: left;\"><input class=\"form-control\" name=\"allianceName\" size=\"16\"/></td>
                            <td colspan=\"2\" style=\"text-align: right;\"><input class=\"form-control\" name=\"submit\" type=\"submit\" value=\"Add Alliance\" /></td>
                        </tr>
                    </table>
                    </form>
                    <form class=\"form-control\" action=\"?menu=whitelist_add&type=corp\" method=\"post\">
                    <table>
                        <tr>
                            <td style=\"text-align: right;\"><font size=\"2\">Corporation Name:</font></td>
                            <td style=\"text-align: left;\"><input class=\"form-control\" name=\"corpName\" size=\"16\"/></td>
                            <td colspan=\"2\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Add Corporation\" /></td>
                        </tr>
                    </table>
                    </form>
                    <form class=\"form-control\" action=\"?menu=whitelist_add&type=char\" method=\"post\">
                    <table>
                        <tr>
                            <td style=\"text-align: right;\"><font size=\"2\">Character Name:</font></td>
                            <td style=\"text-align: left;\"><input class=\"form-control\" name=\"charName\" size=\"16\"/></td>
                            <td colspan=\"2\" style=\"text-align: right;\"><input name=\"submit\" type=\"submit\" value=\"Add Character\" /></td>
                        </tr>
                    </table>
                    </form>");
        printf("</div>");
        
        //Build the white list to print out on the screen
        $whiteList = $db->fetchRowMany('SELECT * FROM Blues');
        $allyList = array();
        $corpList = array();
        $charList = array();
        $allyNum = 0;
        $corpNum = 0;
        $charNum = 0;
        foreach($whiteList as $list) {
            if($list['EntityType'] == 3) {
                $allyList[$allyNum] = $list['EntityID'];
                $allyNum++;
            } else if($list['EntityType'] == 2) {
                $corpList[$corpNum] = $list['EntityID'];
                $corpNum++;
            } else if($list['EntityType'] ==1) {
                $charList[$charNum] = $list['EntityID'];
                $charNum++;
            }
        }
        printf("<div class=\"container\">");
        printf("<table class=\"table-striped\">");
        printf("<tr><td><Type/td><td>Name</td><td>Members</td></tr>");
        for($i = 0; $i < $allyNum; $i++) {
            $ally = $db->fetchRow('SELECT Alliance,Members FROM Alliances WHERE AllianceID= :id', array('id' => $allyList[$i]));
            printf("<tr><td>Alliance</td><td>" . $ally['Alliance'] . "</td><td>" . $ally['Members'] . "</td></tr>");
        }
        for($i = 0; $i < $corpNum; $i++) {
            $corp = $db->fetchRow('SELECT Corporation,Members FROM Corporations WHERE CorporationID= :id', array('id' => $corpList[$i]));
            printf("<tr><td>Corporation</td><td>" . $corp['Corporation'] . "</td><td>" . $corp['Members'] . "</td></tr>");
        }
        for($i = 0; $i < $charNum; $i++) {
            $char = $db->fetchColumn('SELECT Character FROM Characters WHERE CharacterID= :id', array('id' => $charList[$i]));
            printf("<tr><td>Character</td><td>" . $char . "</td><td>N/A</td></tr>.");
        }
        printf("</table>");
        printf("</div>");
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
