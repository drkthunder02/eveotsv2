<?php

/* 
 * ========== * EVE ONLINE TEAMSPEAK V2 BY Lowjack Tzetsu * ==========
 * ========== * EVE ONLINE TEAMSPEAK V2 BASED ON MJ MAVERICK * ============ 
 */

//PHP Debug Mode
error_reporting(E_ALL | E_STRICT);

require_once __DIR__.'/functions/registry.php';

$db = DBOpen();

$session = new Custom\Sessions\session();
$config = new EVEOTS\Config\Config();
$esi = new EVEOTS\ESI\ESI('EVEOTS V2');

//encrypt the unique session id in the form of a key for the form
$_SESSION['key'] = uniqid();
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

//Check to make sure the user is logged in
$username = CheckLogin();
if($username == "") {
    
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}
//Check the security level of the user to see if they are allowed access.
$securityLevel = CheckSecurityLevel($db, $username);
if($securityLevel != 1) {
    printf("div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not authorized to access this area.<br>");
    printf("</div></div>");
    die();
}

//Print the table of Administrators
$admins = $db->fetchRowMany('SELECT * FROM Admins ORDER BY username');
PrintAdminTable($admins, $esi);
//Add Administrator Form
printf("<div class=\"jumbotron\">");
printf("<div class=\"container col-md-4\">");
printf("<h1>Add Administrator</h1><br>");
printf("<form class=\"form-group\" action=\"form/addadmin.php\" method=\"POST\">");
printf("<label for=\"username\">Username</label>");
printf("<input class=\"form-control\" type=\"text\" id=\"username\" name=\"username\">");
printf("<label for=\"password\">Password</label>");
printf("<input class=\"form-control\" type=\"password\" id=\"password\" name=\"password\">");
printf("<label for=\"security\">Security Level</label>");
printf("<select class=\"form-control\" id=\"security\" name=\"security\"><option value=\"1\">1</option><option value=\"2\">2</option></select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"unique\" name=\"unique\" value=\"" . $unique . "\">");
printf("<input class=\"form-control\" type=\"submit\" value=\"Add Administrator\">");
printf("</form>");
printf("</div>");
printf("</div>");
//Edit Administrator Form
printf("<div class=\"jumbotron\">");
printf("<div class=\"container col-md-4\">");
printf("<h1>Edit Administrator</h1><br>");
printf("<form class=\"form-group\" action=\"process/adminedit.php\" method=\"POST\">");
printf("<label for=\"admin\">Admin</label>");
printf("<select class=\"form-control\" id=\"admin\" name=\"admin\">");
foreach($admins as $a) {
    printf("<option value=\"" . $a['username'] . "\">" . $a['username'] . "</option>");
}
printf("</select>");
printf("<label for=\"security\">Security Level</label>");
printf("<select class=\"form-control\" id=\"security\" name=\"security\">");
printf("<option value=\"1\">1</option><option value=\"2\">2</option>");
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"unique\" name=\"unique\" value=\"" . $unique . "\">");
printf("<input class=\"form-control\" type=\"submit\" value=\"Modify Security Level\">");
printf("</div></div>");
//Delete Administrator Form
printf("<div class=\"jumbotron\">");
printf("<div class=\"container col-md-4\">");
printf("<h1>Delete Administrator</h1><br>");
printf("<strong>Note:  This action cannot be undone.</strong>");
printf("<form class=\"form-group\" action=\"process/admindelete.php\" method=\"POST\">");
printf("<label for=\"admin\">Admin</label>");
printf("<select class=\"form-control\" id=\"admin\" name=\"admin\">");
foreach($admins as $a) {
    printf("<option value=\"" . $a['username'] . "\">" . $a['username'] . "</option>");
}
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"unique\" name=\"unique\" value=\"" . $unique . "\">");
printf("<input class=\"form-control\" type=\"submit\" value=\"Delete Administrator\">");
printf("</form>");
printf("</div></div>");
//Close the body and html tags
printf("</body></html>");

?>
