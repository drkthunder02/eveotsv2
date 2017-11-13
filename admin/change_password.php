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
$config = new EVEOTS\Config\Config();
$esi = new EVEOTS\ESI\ESI();

//encrypt the unique session id in the form of a key for the form
$_SESSION['key'] = uniqid();
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintAdminHTMLHeader();
printf("<body style=\"padding-top: 70px\">");
PrintAdminNavBar($db, $_SESSION['EVEOTSusername']);

$username = CheckLogin();
if($username == "") {
    
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}
//Check the security level of the user to see if they are allowed access.
$security = CheckSecurityLevel($db, $username);
if($security['SecurityLevel'] != 1) {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not authorized to access this area.</h1><br>");
    printf("Current security level is: " . $security['SecurityLevel']);
    printf("</div></div>");
    die();
}

//Print the list of Admins and their ids
$admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
if($admins != false || $admins != null) {
    PrintAdminTable($admins, $esi);
}
//Print the form to change the password an admin
printf("<div class=\"container\">");
printf("<form class=\"form-group\" action=\"functions/form/changepass.php\" method=\"POST\">");
printf("<label for=\"username\">Admin Username</label>");
printf("<input class=\"form-control\" type=\"text\" name=\"username\" id=\"username\">");
printf("<label for=\"newPass\">New Password</label>");
printf("<input class=\"form-control\" type=\"password\" name=\"newPass\" id=\"newPass\">");
printf("<label for=\"passConf\">Password Confirm</label>");
printf("<input class=\"form-control\" type=\"password\" name=\"passConf\" id=\"passConf\">");
printf("<br><br>");
printf("<input class=\"form-control\" type=\"submit\" value=\"Change Password\">");
printf("<input class=\"form-control\" type=\"hidden\" name=\"key\" id=\"key\" value=\"" . $unique . "\">");
printf("</form>");
printf("</div>");
//Close the body and html tags
printf("</body></html>");

?>
