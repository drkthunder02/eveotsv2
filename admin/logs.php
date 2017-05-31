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

if($_SESSION['EVEOTSid'] == $config->GetAdminID()) {
    printf("<div class=\"container\">");
    printf("<form class=\"form-group\" action=\"process/clearlogs.php\" method=\"POST\">");
    printf("<label>Clear Logs</label>");
    printf("<input class=\"form-control\" type=\"hidden\" id=\"key\" value=\"" . $unique . "\">");
    printf("<input class=\"form-control\" type=\"submit\" value=\"Clear Logs\">");
    printf("</form>");
}

PrintAdminLogs($db);

printf("</body></html>");

DBClose($db);