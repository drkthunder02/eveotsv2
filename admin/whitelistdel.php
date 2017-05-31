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

//encrypt the unique session id in the form of a key for the form
$_SESSION['key'] = uniqid();
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintAdminHTMLHeader();
PrintAdminNavBar($_SESSION['EVEOTSusername']);

//Check to make sure the user is logged in
$username = CheckLogin();
if($username == "") {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}

printf("<div class=\"container\">");
printf("<div class=\"jumbotron\">");
printf("<h2>If a corporation is in an alliance it is advised <em>not</em> to give the corporation access, but to give the alliance the access. Also remember to keep an eye out for corporations with access joining alliances that shouldn't have it, you should remove these corporations.<br /><strong>TL;DR:</strong> All corporations in your corporation list should NOT be in an alliance.</h2>");
printf("</div>");
printf("</div>");
printf("<div class=\"container\">");
printf("<h1>White List Delete Form</h1><br>");
printf("<form class=\"form-group\" action=\"process/whitelistdel.php\" method=\"POST\">");
printf("<label for=\"entity\">Entity</label>");
printf("<input class=\"form-control\" type=\"text\" id=\"entity\" name=\"entity\">");
printf("<select class=\"form-control\" id=\"type\" name=\"type\">");
printf("<option value=\"Alliance\">Alliance</option>");
printf("<option value=\"Corporation\">Corporation</option>");
printf("<option value=\"Character\">Character</option>");
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" value=\"" . $unique . "\">");
printf("<input class=\"form-control\" type=\"submit\" value=\"White List Delete\">");
printf("</form>");
printf("</div>");

printf("</body></html>");

?>
