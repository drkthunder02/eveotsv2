<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

// PHP debug mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__.'/functions/registry.php';

$db = DBOpen();

$session = new Custom\Sessions\session();

$username = CheckLogin();

PrintAdminHTMLHeader();
PrintAdminNavBar($db, $_SESSION['EVEOTSusername']);

$queryAdmin = $db->fetchColumn('SELECT username FROM Admins WHERE username= :user', array('user' => 'admin'));
if($queryAdmin != null) {
    $installAccount = true;
} else {
    $installAccount = false;
}

if($installAccount == true) {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("Warning: Setup not complete.<br>Users \"admin\" is still in the database.<br>");
    printf("This is a massive security risk.<br>");
    printf("Please create yourself a new account, set it as the root admin, then delete the default \"admin\" account immediately.<br>");
    printf("</div>");
    printf("</div>");
}

if(isset($_GET['msg'])) {
    $msg = filter_input(INPUT_GET, 'msg');
    AdminPanelMsg($msg);
}

printf("<div class=\"jumbotron\">");
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
$admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
PrintAdminTable($admins, $esi);
printf("</div>");

printf("</body>");
printf("</html>");