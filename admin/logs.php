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

//encrypt the unique session id in the form of a key for the form
$_SESSION['key'] = uniqid();
$unique = $_SESSION['key'] . $config->GetSalt();
$unique = md5($unique);

PrintAdminHTMLHeader();
printf("<body style=\"padding-top: 70px\">");
PrintAdminNavBar($db, $_SESSION['EVEOTSusername']);

if($_SESSION['EVEOTSid'] == $config->GetAdminID()) {
    printf("<div class=\"container\">");
    printf("<form action=\"functions/form/clearlogs.php\" method=\"POST\">");
    printf("<div class=\"form-group\">");
    printf("<label>Clear Logs</label>");
    printf("<input class=\"form-control\" type=\"hidden\" id=\"key\" value=\"" . $unique . "\">");
    printf("</div>");
    printf("<button type=\"submit\" class=\"btn btn-default\">Clear Logs</button>");
    printf("</form>");
}

PrintAdminLogs($db);

printf("</body></html>");

DBClose($db);