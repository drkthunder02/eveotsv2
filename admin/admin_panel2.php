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
//$log = new Seat\Eseye\Log\FileLogger();
// Prepare an authentication container for ESI
//$authentication = PrepareESIAuthentication();
// Instantiate a new ESI instance.
//$esi = new Eseye($authentication);
//ESI instances must be created each time for each refresh token

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

//Close Body Tag then HTML Tag
printf("</body>");
printf("</html>");

?> 
