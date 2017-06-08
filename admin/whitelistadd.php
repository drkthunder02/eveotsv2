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

//Check to make sure the user is logged in
$username = CheckLogin();
if($username == "") {
    printf("<div class=\"jumbotron\">");
    printf("<div class=\"container\">");
    printf("<h1>You are not logged in.<br>");
    printf("</div></div>");
    die();
}

$entities = $db->fetchRowMany('SELECT * FROM Blues');
PrintWhiteList($entities);

printf("<div class=\"container\">");
printf("<div class=\"jumbotron\">");
printf("<h2>If a corporation is in an alliance it is advised <em>not</em> to give the corporation access, but to give the alliance the access. Also remember to keep an eye out for corporations with access joining alliances that shouldn't have it, you should remove these corporations.<br /><strong>TL;DR:</strong> All corporations in your corporation list should NOT be in an alliance.</h2>");
printf("</div>");
printf("</div>");
printf("<div class=\"container\">");
printf("<h1>White List Add Form</h1><br>");
printf("<form action=\"functions/form/whitelistadd.php\" method=\"POST\">");
printf("<div class=\"form-group\">");
printf("<label for=\"entity\">Entity</label>");
printf("<input class=\"form-control\" type=\"text\" id=\"entity\" name=\"entity\">");
printf("</div>");
printf("<div class=\"form-group\">");
printf("<select class=\"form-control\" id=\"type\" name=\"type\">");
printf("<option value=\"alliance\">Alliance</option>");
printf("<option value=\"corporation\">Corporation</option>");
printf("<option value=\"character\">Character</option>");
printf("</select>");
printf("<input class=\"form-control\" type=\"hidden\" id=\"key\" name=\"key\" value=\"" . $unique . "\">");
printf("</div>");
printf("<button type=\"submit\" class=\"btn btn-default\">White List Add</button>");
printf("</form>");
printf("</div>");

printf("</body></html>");

?>
